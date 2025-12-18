<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table already exists (created in 2025_11_25_150000_create_module_product_system_tables.php)
        if (!Schema::hasTable('export_layouts')) {
            Schema::create('export_layouts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('report_definition_id')->nullable()->constrained()->onDelete('set null');
                $table->string('layout_name', 100);
                $table->string('entity_type', 50);
                $table->json('selected_columns');
                $table->json('column_order')->nullable();
                $table->json('column_labels')->nullable();
                $table->string('export_format', 20)->default('xlsx');
                $table->boolean('include_headers')->default(true);
                $table->string('date_format', 50)->default('Y-m-d');
                $table->string('number_format', 50)->nullable();
                $table->boolean('is_default')->default(false);
                $table->boolean('is_shared')->default(false);
                $table->timestamps();

                // Indexes
                $table->index(['user_id', 'entity_type']);
                $table->index(['user_id', 'is_default']);
                $table->unique(['user_id', 'entity_type', 'layout_name']);
            });
        } else {
            // Table exists, ensure indexes are present
            Schema::table('export_layouts', function (Blueprint $table) {
                // Check and add missing indexes
                if (!$this->indexExists('export_layouts', 'export_layouts_user_id_entity_type_index')) {
                    $table->index(['user_id', 'entity_type']);
                }
                if (!$this->indexExists('export_layouts', 'export_layouts_user_id_is_default_index')) {
                    $table->index(['user_id', 'is_default']);
                }
                if (!$this->indexExists('export_layouts', 'export_layouts_user_id_entity_type_layout_name_unique')) {
                    $table->unique(['user_id', 'entity_type', 'layout_name']);
                }
            });
        }
    }

    /**
     * Check if an index exists
     */
    protected function indexExists(string $table, string $index): bool
    {
        try {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes($table);
            return isset($indexes[$index]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_layouts');
    }
};
