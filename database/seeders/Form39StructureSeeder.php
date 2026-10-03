<?php

namespace Database\Seeders;

use App\Models\EvaluationAxis;
use App\Models\EvaluationItem;
use App\Models\EvaluationItemOption;
use App\Models\EvaluationScoringRule;
use App\Models\EvaluationSection;
use Illuminate\Database\Seeder;

class Form39StructureSeeder extends Seeder
{
    public function run(): void
    {
        $axes = [
            [
                'code' => 'axis_1',
                'axis_number' => 1,
                'title_ar' => 'المحور الأول: فيدباك الطالب حول المادة الدراسية وطريقة التدريس',
                'title_en' => 'Axis 1: Student feedback on course and teaching method',
                'max_score' => 10,
                'weight_percent' => 10,
                'sections' => [
                    [
                        'code' => 'axis1_bologna_teaching',
                        'title_ar' => 'التدريس وفق مسار بولونيا',
                        'max_score' => 4,
                        'items' => [
                            ['code' => 'axis1_bologna_intro', 'title_ar' => 'تقديم موجز عن عملية التدريس والتقييم وفق مسار بولونيا في أول محاضرة.', 'max_score' => 1, 'input_type' => 'student_boolean', 'is_computed' => true, 'requires_evidence' => false],
                            ['code' => 'axis1_module_guide', 'title_ar' => 'تقديم وشرح دليل وصف المادة الدراسية في الأسبوع الأول للفصل الدراسي.', 'max_score' => 1, 'input_type' => 'student_boolean', 'is_computed' => true, 'requires_evidence' => false],
                            ['code' => 'axis1_formative_assessment', 'title_ar' => 'الالتزام بطريقة التقييم التكويني من حيث الامتحانات القصيرة والواجبات والفعاليات العلمية ومواعيدها.', 'max_score' => 1, 'input_type' => 'student_boolean', 'is_computed' => true, 'requires_evidence' => false],
                            ['code' => 'axis1_outcomes_alignment', 'title_ar' => 'توضيح علاقة مفردات المنهاج الأسبوعي بمخرجات التعلم.', 'max_score' => 1, 'input_type' => 'student_boolean', 'is_computed' => true, 'requires_evidence' => false],
                        ],
                    ],
                    [
                        'code' => 'axis1_lecturer_competence',
                        'title_ar' => 'كفاءة الأستاذ وطريقة التدريس',
                        'max_score' => 6,
                        'items' => [
                            ['code' => 'axis1_interesting_method', 'title_ar' => 'طريقة التدريس والمحاضرة مثيرة للاهتمام وتحفز الطالب لفهم المادة العلمية.', 'max_score' => 1, 'input_type' => 'student_boolean', 'is_computed' => true, 'requires_evidence' => false],
                            ['code' => 'axis1_references', 'title_ar' => 'تزويد الطالب بقائمة من المراجع المختلفة بالإضافة للمراجع الرئيسية.', 'max_score' => 1, 'input_type' => 'student_boolean', 'is_computed' => true, 'requires_evidence' => false],
                            ['code' => 'axis1_questions_time', 'title_ar' => 'إعطاء الوقت الكافي للأسئلة والأجوبة خلال المحاضرة.', 'max_score' => 1, 'input_type' => 'student_boolean', 'is_computed' => true, 'requires_evidence' => false],
                            ['code' => 'axis1_teaching_technology', 'title_ar' => 'استخدام التقنيات وأدوات الصوت والفيديو اللازمة لشرح المحاضرات.', 'max_score' => 1, 'input_type' => 'student_boolean', 'is_computed' => true, 'requires_evidence' => false],
                            ['code' => 'axis1_respectful_treatment', 'title_ar' => 'تعامل المحاضر مع الطلبة باحترام خلال المحاضرة.', 'max_score' => 1, 'input_type' => 'student_boolean', 'is_computed' => true, 'requires_evidence' => false],
                            ['code' => 'axis1_exam_alignment', 'title_ar' => 'أسئلة الامتحان تعكس محتويات المادة العلمية المرتبطة بمخرجات التعلم.', 'max_score' => 1, 'input_type' => 'student_boolean', 'is_computed' => true, 'requires_evidence' => false],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'axis_2',
                'axis_number' => 2,
                'title_ar' => 'المحور الثاني: تقييم حقيبة الأستاذ',
                'title_en' => 'Axis 2: Teaching staff portfolio',
                'max_score' => 60,
                'weight_percent' => 60,
                'sections' => [
                    [
                        'code' => 'axis2_duties',
                        'title_ar' => 'واجبات ومسؤوليات أعضاء هيئة التدريس',
                        'max_score' => 8,
                        'items' => [
                            [
                                'code' => 'axis2_teaching_load',
                                'title_ar' => 'الحمل التدريسي / نصاب عضو هيئة التدريس',
                                'max_score' => 4,
                                'options' => [
                                    ['code' => 'full_load', 'label_ar' => 'اكتمال النصاب', 'score_value' => 4],
                                    ['code' => 'incomplete_load', 'label_ar' => 'عدم اكتمال النصاب', 'score_value' => 2],
                                ],
                                'official_notes_ar' => 'يدعم النظام توثيق حالة رغبة التدريسي بإكمال النصاب وعدم توفر مواد كافية في القسم، مع تكليفه بمهام داخل القسم أو تخص الطلبة لتحقيق النصاب.',
                            ],
                            [
                                'code' => 'axis2_committee_membership',
                                'title_ar' => 'عضوية اللجان العلمية في القسم والكلية والجامعة والوزارة',
                                'max_score' => 4,
                                'options' => [
                                    ['code' => 'permanent_committee', 'label_ar' => 'لجنة دائمة', 'score_value' => 4],
                                    ['code' => 'temporary_committee', 'label_ar' => 'لجنة مؤقتة', 'score_value' => 2],
                                ],
                            ],
                        ],
                    ],
                    [
                        'code' => 'axis2_skills_values',
                        'title_ar' => 'تحقيق مهارات ومعارف وقيم معينة',
                        'max_score' => 5,
                        'items' => [
                            [
                                'code' => 'axis2_appreciation_awards',
                                'title_ar' => 'كتب الشكر والتقدير والجوائز الأكاديمية',
                                'max_score' => 5,
                                'options' => [
                                    ['code' => 'department_appreciation', 'label_ar' => 'كتاب شكر من القسم', 'score_value' => 2],
                                    ['code' => 'college_appreciation', 'label_ar' => 'كتاب شكر من الكلية', 'score_value' => 3],
                                    ['code' => 'university_appreciation', 'label_ar' => 'كتاب شكر من الجامعة', 'score_value' => 4],
                                    ['code' => 'ministry_appreciation', 'label_ar' => 'كتاب شكر من الوزارة', 'score_value' => 5],
                                    ['code' => 'academic_award', 'label_ar' => 'جائزة أكاديمية', 'score_value' => 5],
                                ],
                            ],
                        ],
                    ],
                    [
                        'code' => 'axis2_training_continuing',
                        'title_ar' => 'التدريب والتعليم المستمر',
                        'max_score' => 20,
                        'items' => [
                            [
                                'code' => 'axis2_conduct_training',
                                'title_ar' => 'عقد الدورات التدريبية وورش العمل والندوات العلمية',
                                'max_score' => 6,
                                'options' => [
                                    ['code' => 'training_24h_week', 'label_ar' => 'عقد وتقديم دورة تدريبية لا تقل عن 24 ساعة تدريب وعن أسبوع واحد', 'score_value' => 4],
                                    ['code' => 'conduct_workshop', 'label_ar' => 'عقد وتقديم ورشة عمل', 'score_value' => 2],
                                    ['code' => 'conduct_seminar', 'label_ar' => 'تقديم ندوة أو سمنار', 'score_value' => 2],
                                ],
                            ],
                            [
                                'code' => 'axis2_attend_training_conferences',
                                'title_ar' => 'حضور الدورات التدريبية أو المؤتمرات العلمية ذات العلاقة بالتخصص',
                                'max_score' => 4,
                                'options' => [
                                    ['code' => 'attend_training', 'label_ar' => 'حضور دورة تدريبية', 'score_value' => 2],
                                    ['code' => 'conference_with_paper', 'label_ar' => 'حضور مؤتمر علمي مع تقديم ورقة علمية', 'score_value' => 2],
                                    ['code' => 'conference_without_paper', 'label_ar' => 'حضور مؤتمر علمي دون تقديم ورقة علمية', 'score_value' => 1],
                                ],
                            ],
                            [
                                'code' => 'axis2_attend_seminars_workshops',
                                'title_ar' => 'حضور الندوات العلمية والسمنارات وورش العمل',
                                'max_score' => 10,
                                'options' => [
                                    ['code' => 'attend_seminar_or_workshop', 'label_ar' => 'حضور سمنار أو ورشة عمل', 'score_value' => 1],
                                ],
                            ],
                        ],
                    ],
                    [
                        'code' => 'axis2_research_development',
                        'title_ar' => 'رؤية الجامعة في البحث والتطوير ودعم القطاعات العلمية والصناعية',
                        'max_score' => 20,
                        'items' => [
                            [
                                'code' => 'axis2_research_books_patents',
                                'title_ar' => 'نشر الأبحاث والكتب وبراءات الاختراع',
                                'max_score' => 20,
                                'input_type' => 'research_activity',
                                'options' => [
                                    ['code' => 'wipo_patent', 'label_ar' => 'براءة اختراع دولية مسجلة في WIPO', 'score_value' => 20],
                                    ['code' => 'local_patent', 'label_ar' => 'براءة اختراع محلية', 'score_value' => 5],
                                    ['code' => 'clarivate_scopus_if_below_1', 'label_ar' => 'بحث في مجلة مؤرشفة Clarivate وScopus بمعامل تأثير أقل من 1', 'score_value' => 8],
                                    ['code' => 'clarivate_scopus_if_1_to_5', 'label_ar' => 'بحث في مجلة مؤرشفة Clarivate وScopus بمعامل تأثير من 1 إلى 5', 'score_value' => 10],
                                    ['code' => 'clarivate_scopus_if_above_5', 'label_ar' => 'بحث في مجلة مؤرشفة Clarivate وScopus بمعامل تأثير أكثر من 5', 'score_value' => 12],
                                    ['code' => 'indexed_book_first_author', 'label_ar' => 'كتاب مؤرشف في Scopus أو Clarivate كمؤلف أول مع انتساب للجامعة', 'score_value' => 12],
                                    ['code' => 'indexed_book_second_author', 'label_ar' => 'كتاب مؤرشف كمؤلف ثاني', 'score_value' => 8],
                                    ['code' => 'indexed_book_third_author', 'label_ar' => 'كتاب مؤرشف كمؤلف ثالث', 'score_value' => 6],
                                    ['code' => 'indexed_chapter_first_author', 'label_ar' => 'فصل كتاب مؤرشف كمؤلف أول مع انتساب للجامعة', 'score_value' => 7],
                                    ['code' => 'indexed_chapter_second_author', 'label_ar' => 'فصل كتاب مؤرشف كمؤلف ثاني', 'score_value' => 4],
                                    ['code' => 'university_requested_book', 'label_ar' => 'نشر كتاب بطلب رسمي من الجامعة', 'score_value' => 10],
                                    ['code' => 'scopus_q1', 'label_ar' => 'بحث في مجلة مؤرشفة في Scopus فقط ضمن Q1', 'score_value' => 10],
                                    ['code' => 'scopus_q2', 'label_ar' => 'بحث في مجلة مؤرشفة في Scopus فقط ضمن Q2', 'score_value' => 9],
                                    ['code' => 'scopus_q3', 'label_ar' => 'بحث في مجلة مؤرشفة في Scopus فقط ضمن Q3', 'score_value' => 8],
                                    ['code' => 'scopus_q4', 'label_ar' => 'بحث في مجلة مؤرشفة في Scopus فقط ضمن Q4', 'score_value' => 7],
                                    ['code' => 'non_indexed_doi', 'label_ar' => 'بحث في مجلة غير مؤرشفة بشرط DOI مرتبط بصفحة metadata تتضمن انتساب الباحث', 'score_value' => 5],
                                ],
                            ],
                        ],
                    ],
                    [
                        'code' => 'axis2_international_activity',
                        'title_ar' => 'النشاط الدولي',
                        'max_score' => 5,
                        'items' => [
                            [
                                'code' => 'axis2_international_memberships',
                                'title_ar' => 'عضوية المؤسسات العلمية الدولية',
                                'max_score' => 2,
                                'options' => [
                                    ['code' => 'senior_fellow_member', 'label_ar' => 'عضوية Senior أو Fellow Member في مؤسسة علمية دولية', 'score_value' => 2],
                                    ['code' => 'member', 'label_ar' => 'عضوية Member في مؤسسة علمية دولية', 'score_value' => 1],
                                ],
                            ],
                            [
                                'code' => 'axis2_editorial_boards_reviews',
                                'title_ar' => 'عضوية لجان تحرير المجلات وتقييم بحوثها',
                                'max_score' => 4,
                                'options' => [
                                    ['code' => 'clarivate_editorial_board', 'label_ar' => 'عضوية لجنة تحرير مجلة مؤرشفة في Clarivate', 'score_value' => 4],
                                    ['code' => 'scopus_editorial_board', 'label_ar' => 'عضوية لجنة تحرير مجلة مؤرشفة في Scopus فقط', 'score_value' => 3],
                                    ['code' => 'recognized_nonindexed_editorial_board', 'label_ar' => 'عضوية لجنة تحرير مجلة غير مؤرشفة ومعترف بها', 'score_value' => 1],
                                    ['code' => 'scopus_clarivate_review', 'label_ar' => 'تقييم بحث لمجلة مؤرشفة في Scopus أو Clarivate', 'score_value' => 1],
                                ],
                            ],
                            [
                                'code' => 'axis2_keynote_speaker',
                                'title_ar' => 'متحدث رئيسي في المؤتمرات الدولية والوطنية',
                                'max_score' => 2,
                                'options' => [
                                    ['code' => 'international_keynote', 'label_ar' => 'متحدث رئيسي Keynote speaker في مؤتمر دولي', 'score_value' => 2],
                                    ['code' => 'national_keynote', 'label_ar' => 'متحدث رئيسي في مؤتمر وطني', 'score_value' => 1],
                                ],
                            ],
                        ],
                    ],
                    [
                        'code' => 'axis2_revenue_support',
                        'title_ar' => 'دعم إيرادات الجامعة',
                        'max_score' => 2,
                        'items' => [
                            [
                                'code' => 'axis2_fund_revenue',
                                'title_ar' => 'جلب الأموال إلى الجامعة',
                                'max_score' => 2,
                                'options' => [
                                    ['code' => 'fund_or_project', 'label_ar' => 'الحصول على دعم مالي أو ما يكافئه للجامعة من خلال عمل أو مشروع', 'score_value' => 2],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'axis_3',
                'axis_number' => 3,
                'title_ar' => 'المحور الثالث: تقييم رئيس القسم للعملية التعليمية والأستاذ الجامعي',
                'title_en' => 'Axis 3: Department head evaluation',
                'max_score' => 15,
                'weight_percent' => 15,
                'sections' => [
                    [
                        'code' => 'axis3_department_head',
                        'title_ar' => 'الالتزام برسالة القسم ورؤيته',
                        'max_score' => 15,
                        'items' => [
                            [
                                'code' => 'axis3_commitment_department_vision',
                                'title_ar' => 'رأي رئيس القسم بمدى جدية وتعاون التدريسي مع القسم والعمل على تحقيق أهدافه، خصوصاً تطبيق مسار بولونيا',
                                'max_score' => 15,
                                'input_type' => 'numeric',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'axis_4',
                'axis_number' => 4,
                'title_ar' => 'المحور الرابع: الجانب التربوي والإرشادي',
                'title_en' => 'Axis 4: Educational and guidance aspect',
                'max_score' => 15,
                'weight_percent' => 15,
                'sections' => [
                    [
                        'code' => 'axis4_continuing_quality',
                        'title_ar' => 'المشاركة في لجان التعليم المستمر والجودة',
                        'max_score' => 5,
                        'items' => [
                            [
                                'code' => 'axis4_continuing_education_quality',
                                'title_ar' => 'المشاركة في التعليم المستمر والجودة',
                                'max_score' => 5,
                                'options' => [
                                    ['code' => 'lecturer_continuing_quality', 'label_ar' => 'المشاركة بصفة محاضر في التعليم المستمر والجودة', 'score_value' => 5],
                                    ['code' => 'committee_quality', 'label_ar' => 'المشاركة في لجان الجودة والتعليم المستمر', 'score_value' => 4],
                                    ['code' => 'attendee_quality', 'label_ar' => 'المشاركة بصفة حضور في التعليم المستمر والجودة - حضورين فقط', 'score_value' => 4],
                                    ['code' => 'modern_teaching_methods', 'label_ar' => 'المشاركة في دورات طرائق التدريس الحديثة في التعليم المستمر', 'score_value' => 3],
                                ],
                            ],
                        ],
                    ],
                    [
                        'code' => 'axis4_visits_voluntary_service',
                        'title_ar' => 'الزيارات الميدانية والحقلية والأعمال التطوعية داخل الجامعة أو خارجها وخدمة المجتمع',
                        'max_score' => 10,
                        'items' => [
                            [
                                'code' => 'axis4_field_visits',
                                'title_ar' => 'الزيارات الميدانية للطلبة في مجالات التطبيقات العملية والإنسانية والاجتماعية والعلمية',
                                'max_score' => 10,
                                'options' => [
                                    ['code' => 'field_visit', 'label_ar' => 'زيارة ميدانية للطلبة بغض النظر عن عدد الطلبة وفق الاستمارة المرفقة في الدليل الإرشادي', 'score_value' => 10],
                                ],
                            ],
                            [
                                'code' => 'axis4_voluntary_work_inside_mohe',
                                'title_ar' => 'الأعمال التطوعية داخل وزارة التعليم العالي وتشكيلاتها',
                                'max_score' => 10,
                                'options' => [
                                    ['code' => 'voluntary_work', 'label_ar' => 'عمل تطوعي موثق بكتاب رسمي صادر من التشكيل أو الجامعة', 'score_value' => 5],
                                ],
                            ],
                            [
                                'code' => 'axis4_external_service',
                                'title_ar' => 'خدمات المجتمع والوزارات خارج وزارة التعليم العالي',
                                'max_score' => 10,
                                'options' => [
                                    ['code' => 'external_service', 'label_ar' => 'خدمة أو استشارة أو تدقيق أو ندوة أو ورشة أو محاضرة أو دورة أو مقالة أو خدمة لمؤسسة خارج وزارة التعليم العالي', 'score_value' => 5],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($axes as $axisIndex => $axisData) {
            $sections = $axisData['sections'];
            unset($axisData['sections']);
            $axisData['display_order'] = $axisIndex + 1;
            $axis = EvaluationAxis::updateOrCreate(['code' => $axisData['code']], $axisData);

            foreach ($sections as $sectionIndex => $sectionData) {
                $items = $sectionData['items'];
                unset($sectionData['items']);
                $sectionData['evaluation_axis_id'] = $axis->id;
                $sectionData['display_order'] = $sectionIndex + 1;
                $section = EvaluationSection::updateOrCreate(['code' => $sectionData['code']], $sectionData);

                foreach ($items as $itemIndex => $itemData) {
                    $options = $itemData['options'] ?? [];
                    unset($itemData['options']);
                    $itemData['evaluation_section_id'] = $section->id;
                    $itemData['display_order'] = $itemIndex + 1;
                    $itemData['input_type'] = $itemData['input_type'] ?? 'activity';
                    $itemData['requires_evidence'] = $itemData['requires_evidence'] ?? true;
                    $itemData['is_computed'] = $itemData['is_computed'] ?? false;
                    $item = EvaluationItem::updateOrCreate(['code' => $itemData['code']], $itemData);

                    foreach ($options as $optionIndex => $optionData) {
                        $optionData['evaluation_item_id'] = $item->id;
                        $optionData['display_order'] = $optionIndex + 1;
                        EvaluationItemOption::updateOrCreate([
                            'evaluation_item_id' => $item->id,
                            'code' => $optionData['code'],
                        ], $optionData);
                    }
                }
            }
        }

        $this->seedRules();
    }

    private function seedRules(): void
    {
        $rules = [
            ['scope_type' => 'axis', 'scope_code' => 'axis_1', 'rule_key' => 'student_item_score', 'title_ar' => 'درجة واحدة لكل فقرة', 'description_ar' => 'كل فقرة في فيدباك الطالب تحتسب بدرجة واحدة وبحد أقصى 10 درجات.'],
            ['scope_type' => 'section', 'scope_code' => 'axis2_duties', 'rule_key' => 'section_cap', 'title_ar' => 'سقف الواجبات والمسؤوليات', 'description_ar' => 'تجمع درجات الحمل التدريسي واللجان وتحد بسقف 8 درجات.', 'parameters_json' => ['max' => 8]],
            ['scope_type' => 'section', 'scope_code' => 'axis2_training_continuing', 'rule_key' => 'section_cap', 'title_ar' => 'سقف التدريب والتعليم المستمر', 'description_ar' => 'تجمع درجات التدريب والتعليم المستمر وتحد بسقف 20 درجة.', 'parameters_json' => ['max' => 20]],
            ['scope_type' => 'item', 'scope_code' => 'axis2_research_books_patents', 'rule_key' => 'research_cap', 'title_ar' => 'سقف البحوث والكتب وبراءات الاختراع', 'description_ar' => 'الحد الأقصى لتجميع هذه الفقرة هو 20 درجة بغض النظر عن التحصيل.', 'parameters_json' => ['max' => 20]],
            ['scope_type' => 'item', 'scope_code' => 'axis2_research_books_patents', 'rule_key' => 'first_author_bonus', 'title_ar' => 'إضافة المؤلف الأول', 'description_ar' => 'إذا كان تسلسل الباحث هو الأول تضاف درجة واحدة.', 'parameters_json' => ['bonus' => 1]],
            ['scope_type' => 'item', 'scope_code' => 'axis2_research_books_patents', 'rule_key' => 'corresponding_author_bonus', 'title_ar' => 'إضافة الباحث المراسل', 'description_ar' => 'إذا كان الباحث هو الباحث المراسل تضاف درجة واحدة.', 'parameters_json' => ['bonus' => 1]],
            ['scope_type' => 'item', 'scope_code' => 'axis2_research_books_patents', 'rule_key' => 'author_position_5_to_10', 'title_ar' => 'خصم تسلسل الباحث من الخامس إلى العاشر', 'description_ar' => 'إذا كان تسلسل الباحث من الخامس إلى العاشر تخصم درجتان من تقييم ذلك البحث.', 'parameters_json' => ['from' => 5, 'to' => 10, 'deduction' => 2]],
            ['scope_type' => 'item', 'scope_code' => 'axis2_research_books_patents', 'rule_key' => 'author_position_above_10', 'title_ar' => 'خصم تسلسل الباحث بعد العاشر', 'description_ar' => 'إذا كان تسلسل الباحث أكثر من العاشر يتم خصم 50% من درجات ذلك البحث.', 'parameters_json' => ['above' => 10, 'multiplier' => 0.5]],
            ['scope_type' => 'item', 'scope_code' => 'axis2_research_books_patents', 'rule_key' => 'first_affiliation_required', 'title_ar' => 'شرط الانتساب الأول للجامعة', 'description_ar' => 'لا يؤخذ بالبحوث التي لا يكون انتساب الباحث لجامعته هو الأول، عدا طلبة الدكتوراه حسب الضوابط.'],
            ['scope_type' => 'item', 'scope_code' => 'axis2_research_books_patents', 'rule_key' => 'rank_multiplier', 'title_ar' => 'معامل اللقب العلمي', 'description_ar' => 'تضرب درجة هذه الفقرة بمعامل يعتمد على اللقب العلمي حسب التعليمات والضوابط.', 'is_configurable' => true],
            ['scope_type' => 'penalty', 'scope_code' => 'axis3_commitment_department_vision', 'rule_key' => 'notice_of_attention', 'title_ar' => 'لفت نظر', 'description_ar' => 'خصم 3 درجات.', 'parameters_json' => ['deduction' => 3]],
            ['scope_type' => 'penalty', 'scope_code' => 'axis3_commitment_department_vision', 'rule_key' => 'warning', 'title_ar' => 'إنذار', 'description_ar' => 'خصم 4 درجات.', 'parameters_json' => ['deduction' => 4]],
            ['scope_type' => 'penalty', 'scope_code' => 'axis3_commitment_department_vision', 'rule_key' => 'salary_suspension', 'title_ar' => 'قطع الراتب', 'description_ar' => 'خصم 5 درجات.', 'parameters_json' => ['deduction' => 5]],
            ['scope_type' => 'penalty', 'scope_code' => 'axis3_commitment_department_vision', 'rule_key' => 'reprimand', 'title_ar' => 'توبيخ', 'description_ar' => 'خصم 6 درجات.', 'parameters_json' => ['deduction' => 6]],
            ['scope_type' => 'penalty', 'scope_code' => 'axis3_commitment_department_vision', 'rule_key' => 'salary_reduction', 'title_ar' => 'إنقاص الراتب', 'description_ar' => 'خصم 7 درجات.', 'parameters_json' => ['deduction' => 7]],
            ['scope_type' => 'penalty', 'scope_code' => 'axis3_commitment_department_vision', 'rule_key' => 'rank_reduction', 'title_ar' => 'تنزيل الدرجة', 'description_ar' => 'خصم 8 درجات.', 'parameters_json' => ['deduction' => 8]],
        ];

        foreach ($rules as $rule) {
            EvaluationScoringRule::updateOrCreate([
                'scope_type' => $rule['scope_type'],
                'scope_code' => $rule['scope_code'],
                'rule_key' => $rule['rule_key'],
            ], $rule);
        }
    }
}
