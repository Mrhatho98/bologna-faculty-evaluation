<?php

namespace Tests\Unit;

use App\Models\EvidenceRecord;
use App\Models\TeachingStaff;
use App\Services\EvaluationScoringService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class EvaluationScoringServiceTest extends TestCase
{
    public function test_research_score_excludes_blacklisted_and_missing_affiliation_records(): void
    {
        $staff = new TeachingStaff(['academic_rank' => 'lecturer']);

        $records = new Collection([
            new EvidenceRecord([
                'score_awarded' => 8,
                'metadata_json' => [
                    'is_first_author' => true,
                    'is_corresponding_author' => true,
                    'has_first_university_affiliation' => true,
                    'is_blacklisted_journal' => false,
                    'author_position' => 1,
                ],
            ]),
            new EvidenceRecord([
                'score_awarded' => 12,
                'metadata_json' => [
                    'has_first_university_affiliation' => false,
                    'has_phd_student_exception' => false,
                    'is_blacklisted_journal' => false,
                    'author_position' => 1,
                ],
            ]),
            new EvidenceRecord([
                'score_awarded' => 10,
                'metadata_json' => [
                    'has_first_university_affiliation' => true,
                    'is_blacklisted_journal' => true,
                    'author_position' => 1,
                ],
            ]),
        ]);

        $result = (new EvaluationScoringService())->calculateResearchScore($records, $staff);

        $this->assertSame(10.0, $result['raw_score']);
        $this->assertSame(10.0, $result['capped_raw_score']);
        $this->assertSame(1.6, $result['multiplier']);
        $this->assertSame(16.0, $result['final_score']);
        $this->assertSame(2, $result['excluded_count']);
    }

    public function test_research_score_is_capped_after_rank_multiplier(): void
    {
        $staff = new TeachingStaff(['academic_rank' => 'assistant_lecturer']);

        $records = new Collection([
            new EvidenceRecord([
                'score_awarded' => 20,
                'metadata_json' => [
                    'has_first_university_affiliation' => true,
                    'is_blacklisted_journal' => false,
                    'author_position' => 1,
                ],
            ]),
        ]);

        $result = (new EvaluationScoringService())->calculateResearchScore($records, $staff);

        $this->assertSame(20.0, $result['final_score']);
    }
}
