<?php
class TableView {
    /**
     * Render a data table
     * @param array $columns Associative array ['key' => 'Label'] or ['key' => ['label' => 'Label', 'renderer' => function($row)]]
     * @param array $data Array of data objects/arrays
     * @param string $id Unique ID for the table
     * @param string $class Additional CSS classes
     */
    public static function render($columns, $data, $id = 'dataTable', $class = 'generic-table') {
        ?>
        <div class="table-responsive">
            <table id="<?= $id ?>" class="<?= $class ?>">
                <thead>
                    <tr>
                        <?php $colIndex = 0; ?>
                        <?php foreach ($columns as $key => $col): ?>
                            <?php 
                            $label = is_array($col) ? $col['label'] : $col;
                            $sortType = is_array($col) && isset($col['sortType']) ? $col['sortType'] : 'string';
                            ?>
                            <th data-column="<?= $key ?>" 
                                data-sort-type="<?= $sortType ?>" 
                                onclick="sortTable('<?= $id ?>', <?= $colIndex ?>, '<?= $sortType ?>')"
                                style="cursor: pointer; user-select: none;">
                                <?= $label ?> <i class="fa-solid fa-sort" style="color: #ccc; font-size: 0.8em; margin-left: 5px;"></i>
                            </th>
                            <?php $colIndex++; ?>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="<?= count($columns) ?>" class="text-center">Aucune donnée disponible</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <?php foreach ($columns as $key => $col): ?>
                                    <td>
                                        <?php 
                                        if (is_array($col) && isset($col['renderer']) && is_callable($col['renderer'])) {
                                            echo $col['renderer']($row);
                                        } else {
                                            $val = is_object($row) ? ($row->$key ?? '') : ($row[$key] ?? '');
                                            echo htmlspecialchars($val);
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
?>
