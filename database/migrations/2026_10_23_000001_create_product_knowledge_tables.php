<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_knowledge_entities', function (Blueprint $table) {
            $table->id();
            $table->string('source', 40);
            $table->string('external_id', 191);
            $table->string('entity_type', 40)->index();
            $table->foreignId('parent_id')->nullable()->constrained('product_knowledge_entities')->nullOnDelete();
            $table->string('canonical_label', 255);
            $table->json('labels')->nullable();
            $table->json('descriptions')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('popularity_score')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['source', 'external_id'], 'pke_source_external_unique');
            $table->index(['entity_type', 'is_active', 'popularity_score'], 'pke_type_active_rank_index');
        });

        Schema::create('product_knowledge_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('product_knowledge_entities')->cascadeOnDelete();
            $table->string('locale', 12)->default('und');
            $table->string('term', 255);
            $table->string('normalized_term', 191);
            $table->string('term_type', 32)->default('alias');
            $table->unsignedSmallInteger('weight')->default(50);
            $table->json('source_details')->nullable();
            $table->timestamps();
            $table->unique(['entity_id', 'locale', 'normalized_term', 'term_type'], 'pkt_entity_locale_term_type_unique');
            $table->index(['locale', 'normalized_term'], 'pkt_locale_normalized_index');
            $table->index(['entity_id', 'weight'], 'pkt_entity_weight_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_knowledge_terms');
        Schema::dropIfExists('product_knowledge_entities');
    }
};
