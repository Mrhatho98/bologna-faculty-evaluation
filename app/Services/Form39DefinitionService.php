<?php

namespace App\Services;

use App\Models\EvaluationAxis;
use Illuminate\Support\Facades\Schema;

class Form39DefinitionService
{
    public static function axes(): array
    {
        if (Schema::hasTable('evaluation_axes')) {
            $axes = EvaluationAxis::with(['sections.items.options'])
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get();

            if ($axes->isNotEmpty()) {
                return $axes->map(function (EvaluationAxis $axis) {
                    $items = [];

                    foreach ($axis->sections as $section) {
                        foreach ($section->items as $item) {
                            $items[] = [
                                'key' => $item->code,
                                'axis_number' => $axis->axis_number,
                                'axis_key' => $axis->code,
                                'section_key' => self::legacySectionKey($section->code),
                                'section_code' => $section->code,
                                'section_title' => $section->title_ar,
                                'section_max' => $section->max_score,
                                'title' => $item->title_ar,
                                'max' => $item->max_score,
                                'readonly' => $item->is_computed,
                                'input_type' => $item->input_type,
                                'requires_evidence' => $item->requires_evidence,
                                'research_metadata' => $item->input_type === 'research_activity',
                                'official_notes' => $item->official_notes_ar,
                                'options' => $item->options->map(fn ($option) => [
                                    'code' => $option->code,
                                    'label' => $option->label_ar,
                                    'score' => $option->score_value,
                                ])->values()->all(),
                            ];
                        }
                    }

                    return [
                        'number' => $axis->axis_number,
                        'key' => $axis->code,
                        'title' => $axis->title_ar,
                        'max' => $axis->max_score,
                        'weight' => $axis->weight_percent,
                        'items' => $items,
                    ];
                })->values()->all();
            }
        }

        return self::fallbackAxes();
    }

    public static function editableItems(): array
    {
        return array_values(array_filter(self::items(), fn ($item) => empty($item['readonly'])));
    }

    public static function items(): array
    {
        $items = [];

        foreach (self::axes() as $axis) {
            foreach ($axis['items'] as $item) {
                $items[] = array_merge($item, [
                    'axis_number' => $axis['number'],
                    'axis_key' => $axis['key'],
                    'axis_title' => $axis['title'],
                ]);
            }
        }

        return $items;
    }

    public static function findItem(string $key): ?array
    {
        foreach (self::items() as $item) {
            if ($item['key'] === $key) {
                return $item;
            }
        }

        return null;
    }

    private static function legacySectionKey(string $sectionCode): string
    {
        return match (true) {
            str_starts_with($sectionCode, 'axis1_') => 'student_feedback',
            $sectionCode === 'axis2_duties' => 'duties',
            $sectionCode === 'axis2_skills_values' => 'skills',
            $sectionCode === 'axis2_training_continuing' => 'training',
            $sectionCode === 'axis2_research_development' => 'research',
            $sectionCode === 'axis2_international_activity' => 'international',
            $sectionCode === 'axis2_revenue_support' => 'revenue',
            $sectionCode === 'axis3_department_head' => 'department_head',
            $sectionCode === 'axis4_continuing_quality' => 'continuing_education',
            $sectionCode === 'axis4_visits_voluntary_service' => 'voluntary',
            default => $sectionCode,
        };
    }

    private static function fallbackAxes(): array
    {
        return [
            [
                'number' => 1,
                'key' => 'axis_1',
                'title' => 'المحور الأول: فيدباك الطلبة حول المادة وطريقة التدريس',
                'max' => 10,
                'items' => [
                    [
                        'key' => 'axis1_student_feedback',
                        'section_key' => 'student_feedback',
                        'title' => 'متوسط تقييمات الطلبة للمادة وطريقة التدريس',
                        'max' => 10,
                        'readonly' => true,
                    ],
                ],
            ],
            [
                'number' => 2,
                'key' => 'axis_2',
                'title' => 'المحور الثاني: حقيبة عضو الهيئة التدريسية',
                'max' => 60,
                'items' => [
                    ['key' => 'axis2_teaching_load', 'section_key' => 'duties', 'title' => 'النصاب التدريسي والمهام التدريسية', 'max' => 4],
                    ['key' => 'axis2_committees', 'section_key' => 'duties', 'title' => 'عضوية اللجان والواجبات والمسؤوليات', 'max' => 4],
                    ['key' => 'axis2_appreciation', 'section_key' => 'skills', 'title' => 'كتب الشكر والتقدير والجوائز', 'max' => 5],
                    ['key' => 'axis2_conducted_training', 'section_key' => 'training', 'title' => 'إقامة الدورات والورش والندوات', 'max' => 6],
                    ['key' => 'axis2_attended_conferences', 'section_key' => 'training', 'title' => 'المشاركة في الدورات والمؤتمرات', 'max' => 4],
                    ['key' => 'axis2_attended_workshops', 'section_key' => 'training', 'title' => 'حضور الندوات والورش العلمية', 'max' => 10],
                    ['key' => 'axis2_research_books_patents', 'section_key' => 'research', 'title' => 'البحوث والكتب وبراءات الاختراع', 'max' => 20, 'research_metadata' => true],
                    ['key' => 'axis2_international_activity', 'section_key' => 'international', 'title' => 'النشاط الدولي والتمثيل الخارجي', 'max' => 5],
                    ['key' => 'axis2_revenue_support', 'section_key' => 'revenue', 'title' => 'دعم القطاعات العلمية والصناعية والإيرادات', 'max' => 2],
                ],
            ],
            [
                'number' => 3,
                'key' => 'axis_3',
                'title' => 'المحور الثالث: تقييم رئيس القسم العلمي',
                'max' => 15,
                'items' => [
                    ['key' => 'axis3_department_head_evaluation', 'section_key' => 'department_head', 'title' => 'تقييم الالتزام والأداء من رئيس القسم', 'max' => 15],
                ],
            ],
            [
                'number' => 4,
                'key' => 'axis_4',
                'title' => 'المحور الرابع: الجانب التربوي والإرشادي وخدمة المجتمع',
                'max' => 15,
                'items' => [
                    ['key' => 'axis4_continuing_education_quality', 'section_key' => 'continuing_education', 'title' => 'التعليم المستمر والجودة والاعتماد', 'max' => 5],
                    ['key' => 'axis4_voluntary_visits_service', 'section_key' => 'voluntary', 'title' => 'الزيارات العلمية والعمل التطوعي وخدمة المجتمع', 'max' => 10],
                ],
            ],
        ];
    }
}
