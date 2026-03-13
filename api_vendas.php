<?php
header("Content-Type: application/json; charset=utf-8");

// Token simples para outros sistemas consumirem
$API_TOKEN = "chave";

$token = $_GET['token'] ?? '';
if ($token !== $API_TOKEN) {
    http_response_code(401);
    echo json_encode(["error" => "Não autorizado"]);
    exit;
}

require __DIR__ . "/conexao.php";

// Opcional: filtros simples por data (YYYY-MM-DD)
$data_de  = $_GET['data_de']  ?? null;
$data_ate = $_GET['data_ate'] ?? null;

$sql = "SELECT
            v.id,
            v.data_venda,
            v.quantidade,
            (v.quantidade * p.preco) AS total_venda,
            u.id   AS cliente_id,
            u.nome AS cliente_nome,
            p.id   AS produto_id,
            p.nome AS produto_nome,
            v.vendedor_id,
            v.vendedor_codigo,
            v.comissao_percentual,
            v.comissao_valor
        FROM vendas v
        INNER JOIN usuarios u  ON v.cliente_id = u.id
        INNER JOIN produtos p  ON v.produto_id = p.id
        WHERE 1=1";

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

$sql .= " ORDER BY v.data_venda DESC";

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
    echo json_encode(["error" => "Erro ao consultar vendas"]);
    exit;
}

$vendas = [];
while ($row = $result->fetch_assoc()) {
    $vendas[] = [
        "id"                  => (int)$row["id"],
        "data_venda"          => $row["data_venda"],
        "cliente_id"          => (int)$row["cliente_id"],
        "cliente_nome"        => $row["cliente_nome"],
        "produto_id"          => (int)$row["produto_id"],
        "produto_nome"        => $row["produto_nome"],
        "quantidade"          => (int)$row["quantidade"],
        "total_venda"         => (float)$row["total_venda"],
        "vendedor_id"         => $row["vendedor_id"] !== null ? (int)$row["vendedor_id"] : null,
        "vendedor_codigo"     => $row["vendedor_codigo"],
        "comissao_percentual" => (float)$row["comissao_percentual"],
        "comissao_valor"      => (float)$row["comissao_valor"],
    ];
}

echo json_encode($vendas);
