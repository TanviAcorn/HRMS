<?php
if ($paginator->hasPages()):
?>
<nav>
    <ul class="pagination">
        <?php foreach ($elements as $element): ?>
            <?php if (is_string($element)): ?>
                <?php /* Skip the three dot separator to avoid any arrow-like chars */ ?>
            <?php endif; ?>

            <?php if (is_array($element)): ?>
                <?php foreach ($element as $page => $url): ?>
                    <?php if ($page == $paginator->currentPage()): ?>
                        <li class="page-item active" aria-current="page"><span class="page-link"><?php echo e($page); ?></span></li>
                    <?php else: ?>
                        <li class="page-item"><a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>
<?php endif; ?>
