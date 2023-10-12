<?php

namespace SolidAffiliate\Views\Shared;

class SolidTooltipView
{
    /**
     * @param string $tooltip_body
     * @param null|string $icon_html
     * @return string
     */
    public static function render($tooltip_body, $icon_html = null)
    {
        $default_icon_html = '
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" fill="none" width="16" height="16">
                <path d="M22.5 40H27.5V35H22.5V40ZM25 0C11.2 0 0 11.2 0 25C0 38.8 11.2 50 25 50C38.8 50 50 38.8 50 25C50 11.2 38.8 0 25 0ZM25 45C13.975 45 5 36.025 5 25C5 13.975 13.975 5 25 5C36.025 5 45 13.975 45 25C45 36.025 36.025 45 25 45ZM25 10C19.475 10 15 14.475 15 20H20C20 17.25 22.25 15 25 15C27.75 15 30 17.25 30 20C30 25 22.5 24.375 22.5 32.5H27.5C27.5 26.875 35 26.25 35 20C35 14.475 30.525 10 25 10Z" fill="rgb(34, 113, 177)" />
            </svg>
        ';

        $icon_html = is_null($icon_html) ? $default_icon_html : $icon_html;
        ob_start();
?>
        <div data-html="true" data-sld-tooltip-content="<?php echo ($tooltip_body) ?>" class="sld-tooltip">
            <?php echo ($icon_html) ?>
        </div>
<?php
        return ob_get_clean();
    }

    /**
     * Render a tooltip but with styling for the content.
     *
     * @param string $heading
     * @param string $sub_heading
     * @param string $body
     * @param string $hint
     * 
     * @return string
     */
    public static function render_pretty($heading, $sub_heading, $body, $hint)
    {
        $tooltip_body = self::_render_pretty_tooltip_body($heading, $sub_heading, $body, $hint);
        $tooltip_body = (esc_html(stripslashes($tooltip_body)));
        return self::render($tooltip_body);
    }

    /**
     * Return the tooltip body for the pretty tooltip.
     *
     * @param string $heading
     * @param string $sub_heading
     * @param string $body
     * @param string $hint
     * @param string $max_width
     * 
     * @return string
     */
    public static function _render_pretty_tooltip_body($heading, $sub_heading, $body, $hint, $max_width = '800px')
    {
        $tooltip_body = "
        <div class='sld-tooltip-box' style='max-width: {$max_width}'>
            <p class='sld-tooltip_heading'>{$heading}</p>
            <p class='sld-tooltip_sub-heading'>{$sub_heading}</p>
            <p class='sld-tooltip_body'>{$body}</p>
            <p class='sld-tooltip_hint'>{$hint}</p>
        </div>
        ";

        return $tooltip_body;
    }
}
