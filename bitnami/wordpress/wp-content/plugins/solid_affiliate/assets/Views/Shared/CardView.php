<?php

namespace SolidAffiliate\Views\Shared;

class CardView
{

    /**
     * Undocumented function
     * 
     * @param string $title
     * @param string $body
     * 
     * @return string
     */
    public static function render($title, $body)
    {
        ob_start();
?>

        <div class="card" style="display: inline-table">
            <h2 class="title"><?php echo ($title); ?></h2>
            <p>
                <?php echo ($body) ?>
            </p>
        </div>


<?php
        return ob_get_clean();
    }
}
