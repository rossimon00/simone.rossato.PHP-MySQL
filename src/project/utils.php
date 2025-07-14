<?php
// utils.php

/**
 * Converte una data da formato 'd-m-Y' a formato MySQL 'Y-m-d'.
 * 
 * @param string $dateStr Data in formato 'giorno-mese-anno' (es. '15-07-2025')
 * @return string|false Data in formato MySQL 'anno-mese-giorno' (es. '2025-07-15') o false se formato non valido
 */
function convertDateToMySQLFormat(string $dateStr): string|false {
    $dt = DateTime::createFromFormat('d-m-Y', $dateStr);
    if ($dt !== false) {
        return $dt->format('Y-m-d');
    }
    return false;
}
