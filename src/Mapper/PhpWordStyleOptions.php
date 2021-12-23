<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Mapper;

class PhpWordStyleOptions
{
    public static function getMappedStyleOptions(array $options): array
    {
        $mappedStyles = [
            'section' => null,
            'font' => null,
            'paragraph' => null,
            'table' => null,
            'row' => null,
            'cell' => null,
            'image' => null,
            'numberingLevel' => null,
            'chart' => null,
            'toc' => null,
            'line' => null,
        ];

        foreach ($options as $option => $value) {
            if (self::isSectionStyleOption($option)) {
                $mappedStyles['section'][$option] = $value;
            }
            if (self::isFontStyleOption($option)) {
                $mappedStyles['font'][$option] = $value;
            }
            if (self::isParagraphStyleOption($option)) {
                $mappedStyles['paragraph'][$option] = $value;
            }
            if (self::isTableStyleOption($option)) {
                $mappedStyles['table'][$option] = $value;
            }
            if (self::isRowStyleOption($option)) {
                $mappedStyles['row'][$option] = $value;
            }
            if (self::isCellStyleOption($option)) {
                $mappedStyles['cell'][$option] = $value;
            }
            if (self::isImageStyleOption($option)) {
                $mappedStyles['image'][$option] = $value;
            }
            if (self::isNumberingLevelStyleOption($option)) {
                $mappedStyles['numberingLevel'][$option] = $value;
            }
            if (self::isChartStyleOption($option)) {
                $mappedStyles['chart'][$option] = $value;
            }
            if (self::isTocStyleOption($option)) {
                $mappedStyles['toc'][$option] = $value;
            }
            if (self::isLineStyleOption($option)) {
                $mappedStyles['line'][$option] = $value;
            }
        }
        return $mappedStyles;
    }

    private static function isSectionStyleOption(string $option): bool
    {
        return in_array($option, [
            'borderColor',
            'borderSize',
            'borderBottomColor',
            'borderBottomSize',
            'borderLeftColor',
            'borderLeftSize',
            'borderRightColor',
            'borderRightSize',
            'borderTopColor',
            'borderTopSize',
            'breakType',
            'colsNum',
            'colsSpace',
            'footerHeight',
            'gutter',
            'headerHeight',
            'marginTop',
            'marginLeft',
            'marginRight',
            'marginBottom',
            'orientation',
            'pageSizeH',
            'pageSizeW',
            'vAlign',
        ], true);
    }

    private static function isFontStyleOption(string $option): bool
    {
        return in_array($option, [
            'allCaps',
            'bgColor',
            'bold',
            'color',
            'doubleStrikethrough',
            'fgColor',
            'hint',
            'italic',
            'name',
            'rtl',
            'size',
            'smallCaps',
            'strikethrough',
            'subScript',
            'superScript',
            'underline',
            'lang',
            'position',
            'hidden',
        ], true);
    }

    private static function isParagraphStyleOption(string $option): bool
    {
        return in_array($option, [
            'alignment',
            'basedOn',
            'hanging',
            'indent',
            'indentation',
            'keepLines',
            'keepNext',
            'lineHeight',
            'next',
            'pageBreakBefore',
            'spaceBefore',
            'spaceAfter',
            'spacing',
            'spacingLineRule',
            'suppressAutoHyphens',
            'tabs',
            'widowControl',
            'contextualSpacing',
            'bidi',
            'shading',
            'textAlignment',
        ], true);
    }

    private static function isTableStyleOption(string $option): bool
    {
        return in_array($option, [
            'alignment',
            'bgColor',
            'borderColor',
            'borderSize',
            'borderBottomColor',
            'borderBottomSize',
            'borderLeftColor',
            'borderLeftSize',
            'borderRightColor',
            'borderRightSize',
            'borderTopColor',
            'borderTopSize',
            'cellMargin',
            'cellMarginTop',
            'cellMarginRight',
            'cellMarginBottom',
            'cellMarginLeft',
            'indent',
            'width',
            'unit',
            'layout',
            'cellSpacing',
            'position',
            'bidiVisual',
            'leftFromText',
            'rightFromText',
            'topFromText',
            'bottomFromText',
            'vertAnchor',
            'horzAnchor',
            'tblpXSpec',
            'tblpX',
            'tblpYSpec',
            'tblpY',
        ], true);
    }

    private static function isRowStyleOption(string $option): bool
    {
        return in_array($option, [
            'cantSplit',
            'exactHeight',
            'tblHeader',
        ], true);
    }

    private static function isCellStyleOption(string $option): bool
    {
        return in_array($option, [
            'bgColor',
            'borderColor',
            'borderSize',
            'borderStyle',
            'borderBottomColor',
            'borderBottomSize',
            'borderBottomStyle',
            'borderLeftColor',
            'borderLeftSize',
            'borderLeftStyle',
            'borderRightColor',
            'borderRightSize',
            'borderRightStyle',
            'borderTopColor',
            'borderTopSize',
            'borderTopStyle',
            'gridSpan',
            'textDirection',
            'valign',
            'vMerge',
            'width',
        ], true);
    }

    private static function isImageStyleOption(string $option): bool
    {
        return in_array($option, [
            'alignment',
            'height',
            'marginLeft',
            'marginTop',
            'width',
            'wrappingStyle',
            'wrapDistanceTop',
            'wrapDistanceBottom',
            'wrapDistanceLeft',
            'wrapDistanceRight',
        ], true);
    }

    private static function isNumberingLevelStyleOption(string $option): bool
    {
        return in_array($option, [
            'alignment',
            'font',
            'format',
            'hanging',
            'hint',
            'left',
            'restart',
            'start',
            'suffix',
            'tabPos',
            'text',
        ], true);
    }

    private static function isChartStyleOption(string $option): bool
    {
        return in_array($option, [
            'width',
            'height',
            '3d',
            'colors',
            'title',
            'showLegend',
            'LegendPosition',
            'categoryLabelPosition',
            'valueLabelPosition',
            'categoryAxisTitle',
            'valueAxisTitle',
            'majorTickMarkPos',
            'showAxisLabels',
            'gridX',
            'gridY',
        ], true);
    }

    private static function isTocStyleOption(string $option): bool
    {
        return in_array($option, [
            'tabLeader',
            'tabPos',
            'indent',
        ], true);
    }

    private static function isLineStyleOption(string $option): bool
    {
        return in_array($option, [
            'weight',
            'color',
            'dash',
            'beginArrow',
            'endArrow',
            'width',
            'height',
            'flip',
        ], true);
    }

}
