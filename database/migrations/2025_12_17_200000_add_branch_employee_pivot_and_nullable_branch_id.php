<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create branch_employee pivot table for many-to-many relationship
        Schema::create('branch_employee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('employee_id');
            $table->boolean('is_primary')->default(false)->comment('Is this the employee\'s primary branch');
            $table->date('assigned_at')->nullable();
            $table->date('detached_at')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('hr_employees')->onDelete('cascade');
            
            $table->unique(['branch_id', 'employee_id']);
            $table->index('is_primary');
        });

        // Make branch_id nullable on hr_employees table to support multi-branch assignments
        Schema::table('hr_employees', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_employee');
        
        // Restore branch_id to not nullable (with safe fallback to first branch)
        if (Schema::hasTable('hr_employees')) {
            DB::statement('UPDATE hr_employees SET branch_id = (SELECT MIN(id) FROM branches) WHERE branch_id IS NULL');
            
            Schema::table('hr_employees', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable(false)->change();
            });
        }
    }
};
