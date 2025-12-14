<?php
// Helper functions for MySQLi results
function fetchAll($stmt) {
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function fetchOne($stmt) {
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function fetchColumn($stmt) {
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_row()) {
        $rows[] = $row[0];
    }
    return $rows;
}
?>
