<?php
header("Content-Type: application/json; charset=utf-8");

// Mesmo token simples usado em api_vendas.php
$API_TOKEN = "chave";

$token = $_GET['token'] ?? '';
if ($token !== $API_TOKEN) {
    http_response_code(401);
    echo json_encode(["error" => "Não autorizado"]);
    exit;
}

require __DIR__ . "/conexao.php";

// Filtros opcionais por data de venda (YYYY-MM-DD)
$data_de  = $_GET['data_de']  ?? null;
$data_ate = $_GET['data_ate'] ?? null;

$sql = "SELECT
            ven.id      AS vendedor_id,
            ven.codigo  AS vendedor_codigo,
            ven.nome    AS vendedor_nome,
            SUM(v.comissao_valor)                 AS total_comissao,
            SUM(v.total_venda)                    AS total_vendas,
            COUNT(v.id)                           AS total_vendas_registros
        FROM vendas v
        LEFT JOIN vendedores ven ON v.vendedor_id = ven.id
        WHERE v.vendedor_id IS NOT NULL";

$params = [];
$types  = "";

if ($data_de) {
    $sql .= " AND DATE(v.data_venda) >= ?";
    $types .= "s";
    $params[] = $data_de;
}
if ($data_ate) {
    $sql .= " AND DATE(v.data_venda) <= ?";
    $types .= "s";
    $params[] = $data_ate;
}

$sql .= " GROUP BY ven.id, ven.codigo, ven.nome
          ORDER BY total_comissao DESC";

if ($types) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao consultar comissões"]);
    exit;
}

$comissoes = [];
while ($row = $result->fetch_assoc()) {
    $comissoes[] = [
        "vendedor_id"            => (int)$row["vendedor_id"],
        "vendedor_codigo"        => $row["vendedor_codigo"],
        "vendedor_nome"          => $row["vendedor_nome"],
        "total_comissao"         => (float)$row["total_comissao"],
        "total_vendas"           => (float)$row["total_vendas"],
        "total_vendas_registros" => (int)$row["total_vendas_registros"],
    ];
}

echo json_encode($comissoes);
