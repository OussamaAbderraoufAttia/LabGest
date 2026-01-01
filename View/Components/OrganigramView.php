<?php
class OrganigramView {
    /**
     * Render a hierarchical tree
     * @param array $root Node data (Head of the tree)
     * @param array $children Array of children nodes or grouped children
     * @param callable $nodeRenderer Function($node) returning HTML content for a node
     */
    public static function renderTree($root, $childrenGroups, $nodeRenderer) {
        ?>
        <div class="tree">
            <ul>
                <li>
                    <div class="node-container root-node">
                        <?= $nodeRenderer($root) ?>
                    </div>
                    <?php if (!empty($childrenGroups)): ?>
                        <ul>
                            <?php foreach ($childrenGroups as $groupName => $members): ?>
                                <?php if (!empty($members)): ?>
                                    <li>
                                        <div class="node-group-label"><?= $groupName ?></div>
                                        <ul>
                                            <?php foreach ($members as $member): ?>
                                                <li>
                                                    <div class="node-container">
                                                        <?= $nodeRenderer($member) ?>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
        <?php
    }
}
?>
