<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Mapper;

class PhpWordStyleOptions
{
    public function getMappedStyleOptions(array $options): array
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
            if ($this->isSectionStyleOption($option)) {
                $mappedStyles['section'][$option] = $value;
            }
            if ($this->isFontStyleOption($option)) {
                $mappedStyles['font'][$option] = $value;
            }
            if ($this->isParagraphStyleOption($option)) {
                $mappedStyles['paragraph'][$option] = $value;
            }
            if ($this->isTableStyleOption($option)) {
                $mappedStyles['table'][$option] = $value;
            }
            if ($this->isRowStyleOption($option)) {
                $mappedStyles['row'][$option] = $value;
            }
            if ($this->isCellStyleOption($option)) {
                $mappedStyles['cell'][$option] = $value;
            }
            if ($this->isImageStyleOption($option)) {
                $mappedStyles['image'][$option] = $value;
            }
            if ($this->isNumberingLevelStyleOption($option)) {
                $mappedStyles['numberingLevel'][$option] = $value;
            }
            if ($this->isChartStyleOption($option)) {
                $mappedStyles['chart'][$option] = $value;
            }
            if ($this->isTocStyleOption($option)) {
                $mappedStyles['toc'][$option] = $value;
            }
            if ($this->isLineStyleOption($option)) {
                $mappedStyles['line'][$option] = $value;
            }
        }
        return $mappedStyles;
    }

    private function isSectionStyleOption(string $option): bool
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

    private function isFontStyleOption(string $option): bool
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

    private function isParagraphStyleOption(string $option): bool
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

    private function isTableStyleOption(string $option): bool
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

    private function isRowStyleOption(string $option): bool
    {
        return in_array($option, [
            'cantSplit',
            'exactHeight',
            'tblHeader',
        ], true);
    }

    private function isCellStyleOption(string $option): bool
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

    private function isImageStyleOption(string $option): bool
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

    private function isNumberingLevelStyleOption(string $option): bool
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

    private function isChartStyleOption(string $option): bool
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

    private function isTocStyleOption(string $option): bool
    {
        return in_array($option, [
            'tabLeader',
            'tabPos',
            'indent',
        ], true);
    }

    private function isLineStyleOption(string $option): bool
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
