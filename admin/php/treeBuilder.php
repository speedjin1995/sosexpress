<?php
// Function to build a nested array representing the module hierarchy
function buildModuleTree($conn, $parentId = null) {
    $tree = [];
    $sql = "SELECT * FROM modules WHERE parent_module_id " . ($parentId === '0' ? "= '0'" : " = $parentId");
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $submodules = buildModuleTree($conn, $row['id']);
            if (!empty($submodules)) {
                $row['submodules'] = $submodules;
            }
            $tree[] = $row;
        }
    }

    return $tree;
}

// Function to display modules with their levels
function displayModulesWithLevels($modules, $level = 1) {
    foreach ($modules as $module) {
        echo str_repeat('-', $level) . ' ' . $module['module_name'] . ' (Level ' . $level . ')' . PHP_EOL;
        if (isset($module['submodules'])) {
            displayModulesWithLevels($module['submodules'], $level + 1);
        }
    }
}

function displayModulesInNestedTable($modules) {
    echo '<table style="width: 100%; border-collapse: collapse;"><tbody>';
    
    foreach ($modules as $module) {
        echo '<tr class="main">';
        echo '<td><input type="checkbox" name="selectedModules[]" value="' . $module['id'] . '" ' . ($module['parent_module_id'] == '0' ? 'class="level1"' : 'class="inner"') . '></td>';
        echo '<td ' . ($module['parent_module_id'] == '0' ? 'style="font-weight: bold;"' : '') . '>' . $module['module_name'];

        if (isset($module['submodules'])) {
            echo '<br>'; // Start a new cell for the nested table
            echo '<table style="width: 100%; border-collapse: collapse; border-top: none;"><tbody>';

            for($i=0; $i<count($module['submodules']); $i++){
                echo '<tr>';
                echo '<td><input type="checkbox" name="selectedModules[]" value="' . $module['submodules'][$i]['id'] . '" ' . ($module['submodules'][$i]['parent_module_id'] == '0' ? 'class="level1"' : 'class="inner"') . '></td>';
                echo '<td ' . ($module['submodules'][$i]['parent_module_id'] == '0' ? 'style="font-weight: bold;"' : '') . '>' . $module['submodules'][$i]['module_name'] . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table></td>';
        }
        else{
            echo '</td>';
        }

        /*if (isset($module['submodules'])) {
            echo '<td>'; // Start a new cell for the nested table
            echo '<table style="width: 100%; border-collapse: collapse; border-top: none;"><tbody><tr>';
            displayModulesInNestedTable($module['submodules'], $level + 1);
            echo '</tr></tbody></table>';
            echo '</td>';
        }*/
        
        //echo '</tr>';
    }
    
    echo '</tbody></table>';
}

?>