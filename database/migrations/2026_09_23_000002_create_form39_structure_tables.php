<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_axes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedTinyInteger('axis_number');
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->decimal('max_score', 6, 2);
            $table->decimal('weight_percent', 5, 2);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('evaluation_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_axis_id')->constrained('evaluation_axes')->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->decimal('max_score', 6, 2);
            $table->string('aggregation_method')->default('sum_capped');
            $table->text('description_ar')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('evaluation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_section_id')->constrained('evaluation_sections')->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->decimal('max_score', 6, 2);
            $table->string('input_type')->default('activity');
            $table->boolean('requires_evidence')->default(true);
            $table->boolean('is_computed')->default(false);
            $table->text('official_notes_ar')->nullable();
            $table->json('metadata_schema_json')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('evaluation_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_item_id')->constrained('evaluation_items')->onDelete('cascade');
            $table->string('code');
            $table->string('label_ar');
            $table->string('label_en')->nullable();
            $table->decimal('score_value', 6, 2);
            $table->json('metadata_json')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['evaluation_item_id', 'code']);
        });

        Schema::create('evaluation_scoring_rules', function (Blueprint $table) {
            $table->id();
            $table->string('scope_type');
            $table->string('scope_code');
            $table->string('rule_key');
            $table->string('title_ar');
            $table->text('description_ar')->nullable();
            $table->json('parameters_json')->nullable();
            $table->boolean('is_configurable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['scope_type', 'scope_code', 'rule_key'], 'eval_rules_scope_unique');
        });

        Schema::create('evaluation_activity_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->foreignId('evaluation_item_id')->constrained('evaluation_items')->onDelete('cascade');
            $table->foreignId('evaluation_item_option_id')->nullable()->constrained('evaluation_item_options')->onDelete('set null');
            $table->string('title')->nullable();
            $table->text('description');
            $table->string('evidence_url');
            $table->date('activity_date')->nullable();
            $table->decimal('score_awarded', 6, 2)->default(0);
            $table->json('metadata_json')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['evaluation_id', 'evaluation_item_id'], 'eval_activity_eval_item_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_activity_records');
        Schema::dropIfExists('evaluation_scoring_rules');
        Schema::dropIfExists('evaluation_item_options');
        Schema::dropIfExists('evaluation_items');
        Schema::dropIfExists('evaluation_sections');
        Schema::dropIfExists('evaluation_axes');
    }
};
