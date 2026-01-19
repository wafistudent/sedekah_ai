<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $teams       = config('permission.teams');
        $tableNames  = config('permission.table_names');
        $columnNames = config('permission.column_names');

        $pivotRole       = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
            $table->bigIncrements('id');

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key']);
            }

            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $teams
                ? $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name'])
                : $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use (
            $tableNames,
            $columnNames,
            $pivotPermission,
            $teams
        ) {
            $table->unsignedBigInteger($pivotPermission);

            $table->string('model_type');
            $table->string($columnNames['model_morph_key'], 20); // 👈 USERNAME

            $table->index(
                [$columnNames['model_morph_key'], 'model_type'],
                'model_has_permissions_model_id_model_type_index'
            );

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key']);

                $table->primary([
                    $columnNames['team_foreign_key'],
                    $pivotPermission,
                    $columnNames['model_morph_key'],
                    'model_type',
                ]);
            } else {
                $table->primary([
                    $pivotPermission,
                    $columnNames['model_morph_key'],
                    'model_type',
                ]);
            }
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use (
            $tableNames,
            $columnNames,
            $pivotRole,
            $teams
        ) {
            $table->unsignedBigInteger($pivotRole);

            $table->string('model_type');
            $table->string($columnNames['model_morph_key'], 20); // 👈 USERNAME

            $table->index(
                [$columnNames['model_morph_key'], 'model_type'],
                'model_has_roles_model_id_model_type_index'
            );

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key']);

                $table->primary([
                    $columnNames['team_foreign_key'],
                    $pivotRole,
                    $columnNames['model_morph_key'],
                    'model_type',
                ]);
            } else {
                $table->primary([
                    $pivotRole,
                    $columnNames['model_morph_key'],
                    'model_type',
                ]);
            }
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use (
            $tableNames,
            $pivotRole,
            $pivotPermission
        ) {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            $table->primary([$pivotPermission, $pivotRole]);
        });
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
