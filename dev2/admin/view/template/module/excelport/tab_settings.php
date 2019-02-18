<table class="form">
    <tr>
        <td>
            <?php echo $text_export_entries_number; ?>
        </td>
        <td>
            <input type="number" min="50" max="800" name="ExcelPort[Settings][ExportLimit]" value="<?php echo !empty($data['ExcelPort']['Settings']['ExportLimit']) ? $data['ExcelPort']['Settings']['ExportLimit'] : '500'; ?>" />
        </td> 
    </tr>
    <tr>
        <td>
            <?php echo $text_import_limit; ?>
        </td>
        <td>
            <input type="number" min="10" max="800" name="ExcelPort[Settings][ImportLimit]" value="<?php echo !empty($data['ExcelPort']['Settings']['ImportLimit']) ? $data['ExcelPort']['Settings']['ImportLimit'] : '100'; ?>" />
        </td> 
    </tr>
</table>