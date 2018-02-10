<?php

namespace app\components;


class RenderUtils
{
    public static function getTableHeader($rows) {
        $tableHeader = [];
        if (!empty($rows) && isset($rows[0])) {
            $firstRow = $rows[0];
            foreach (array_keys($firstRow) as $col) {
                $tableHeader[] = ucfirst($col);
            }
        }
        return $tableHeader;
    }

    public static function renderTable($tableHeader, $rows) {
        $table = '<table class="">';
        $table .= '<tr>';
        foreach ($tableHeader as $col) {
            $table .= "<th>{$col}</th>";
        }
        $table .= '</tr>';
        foreach ($rows as $row) {
            $table .= '<tr>';
            foreach ($row as $col) {
                $table .= "<td>{$col}</td>";
            }
            $table .= '</tr>';
        }
        $table .= '</table>';
        return $table;
    }

    public static function renderQueryStats($queryStats) {
        $result = '';
        if (!empty($queryStats) && isset($queryStats[1])) {
            $queryStats = $queryStats[1];
            $sql = $queryStats['sql'];
            $params = $queryStats['params'];
            $executionMS = $queryStats['executionMS'];
            $result = '<b>Execution MS:</b> ' . $executionMS . '<br /><br />';
            $result .= '<b>Query:</b> <br /><small>' . nl2br($sql) . '</small><br />';
//            $result .= '<b>Query:</b> <pre><code><small>' . ($sql) . '</small></code></pre><br />';
            $result .= '<b>Params:</b>';
            if (!empty($params)) {
                $result .= '<ul>';
                foreach ($params as $k => $v) {
                    $result .= '<li>' . $k . ' => ' . $v . '</li>';
                }
                $result .= '</ul>';
            }
        }
        return $result;
    }
}