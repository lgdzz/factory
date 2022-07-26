<html>
<meta charset="utf-8">
<title><?php echo $this->project_name ? $this->project_name . '-' : ''; ?>数据字典</title>
<style>
    body, td, th {
        font-family: "宋体";
        font-size: 12px;
    }

    table, h1, p {
        margin: 0px auto;
    }

    table {
        border-collapse: collapse;
        border: 1px solid #CCC;
        background: #efefef;
    }

    table caption {
        text-align: left;
        background-color: #fff;
        line-height: 2em;
        font-size: 14px;
        font-weight: bold;
    }

    table th {
        text-align: left;
        font-weight: bold;
        height: 26px;
        line-height: 26px;
        font-size: 12px;
        border: 1px solid #CCC;
        padding-left: 5px;
    }

    table td {
        height: 20px;
        font-size: 12px;
        border: 1px solid #CCC;
        background-color: #fff;
        padding-left: 5px;
    }

    .c1 {
        width: 150px;
    }

    .c2 {
        width: 150px;
    }

    .c3 {
        width: 80px;
    }

    .c4 {
        width: 100px;
    }

    .c5 {
        width: 100px;
    }

    .c6 {
        width: 300px;
    }

    .menu {
        position: fixed;
        top: 50px;
        left: 50px;
        width: 300px;
        height: 800px;
        overflow: auto;
    }

    .menu a {
        /*color: #e91e63;*/
    }

    .menu .comment {
        color: #ccc;
        float: right;
    }

    .tables{
        width: 960px;
        margin-left: 400px;
    }

    .table {
        box-shadow: 0px 0px 15px #ccc;
        margin: 0 auto;
        padding: 30px;
        margin-bottom: 30px;
        margin-top: 30px;
        border-radius: 5px;
    }

    .table-name > div:nth-child(1) {
        float: left;
    }

    .table-name > div:nth-child(2) {
        float: right;
    }
</style>
<body>
<div class="menu">
    <?php
    foreach ($this->tables as $table) {
        $comment = '<span class="comment">（' . ($table['TABLE_COMMENT'] ?: '-') . '）</span>';
        echo '<div><a href="#table_' . $table['TABLE_NAME'] . '">' . $table['TABLE_NAME'] . '</a>' . $comment . '</div>';
    }
    ?>
    <p style="text-align:left;margin:20px auto;">总共：<?php echo count($this->tables); ?></p>
</div>
<div class="tables">
    <h1 style="text-align:center;"><?php echo $this->dbname; ?>数据字典</h1>
    <p style="text-align:center;margin:20px auto;">生成时间：<?php echo date('Y年m月d日 H:i'); ?></p>
    <?php
    foreach ($this->tables as $table) {
        echo '<div class="table" id="table_' . $table['TABLE_NAME'] . '"><table border="1" cellspacing="0" cellpadding="0" align="center">';
        echo '<caption class="table-name"><div>表名：' . $table['TABLE_NAME'] . '</div><div>备注：' . ($table['TABLE_COMMENT'] ?: '-') . '</div></caption>';
        echo '<tbody>';
        echo '<tr><th>字段名</th><th>数据类型</th><th>默认值</th><th>允许非空</th><th>自动递增</th><th>备注</th></tr>';
        foreach ($table['COLUMN'] as $f) {
            echo '<tr>';
            echo '<td class="c1">' . $f['COLUMN_NAME'] . '</td>';
            echo '<td class="c2">' . $f['COLUMN_TYPE'] . '</td>';
            echo '<td class="c3">' . $f['COLUMN_DEFAULT'] . '</td>';
            echo '<td class="c4">' . $f['IS_NULLABLE'] . '</td>';
            echo '<td class="c5">' . ($f['EXTRA'] == 'auto_increment' ? '是' : ' ') . '</td>';
            echo '<td class="c6">' . $f['COLUMN_COMMENT'] . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    }
    ?>
</div>
</body>
</html>