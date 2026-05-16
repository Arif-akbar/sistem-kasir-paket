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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address_line');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code', 12)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('operating_hours')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('role')->constrained('branches')->nullOnDelete();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('awb')->unique();
            $table->foreignId('origin_branch_id')->constrained('branches');
            $table->foreignId('destination_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('sender_name');
            $table->string('sender_phone', 32);
            $table->text('sender_address');
            $table->string('recipient_name');
            $table->string('recipient_phone', 32);
            $table->text('recipient_address');
            $table->string('destination_city');
            $table->string('zone_code', 16)->default('LOCAL')->index();
            $table->string('service_type', 24)->default('regular')->index();
            $table->string('content_description')->nullable();
            $table->decimal('actual_weight_kg', 8, 2);
            $table->decimal('length_cm', 8, 2)->default(0);
            $table->decimal('width_cm', 8, 2)->default(0);
            $table->decimal('height_cm', 8, 2)->default(0);
            $table->decimal('volumetric_weight_kg', 8, 2)->default(0);
            $table->decimal('billable_weight_kg', 8, 2);
            $table->decimal('declared_value', 14, 2)->default(0);
            $table->string('status', 24)->default('paid')->index();
            $table->string('inspection_label')->nullable();
            $table->decimal('inspection_confidence', 5, 2)->nullable();
            $table->timestamp('sla_due_at')->nullable()->index();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no')->unique();
            $table->foreignId('package_id')->unique()->constrained('packages')->cascadeOnDelete();
            $table->foreignId('cashier_id')->constrained('users');
            $table->foreignId('branch_id')->constrained('branches');
            $table->decimal('subtotal', 14, 2);
            $table->decimal('insurance_fee', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2);
            $table->decimal('amount_paid', 14, 2);
            $table->decimal('change_due', 14, 2)->default(0);
            $table->string('payment_method', 24)->default('cash')->index();
            $table->string('payment_status', 24)->default('paid')->index();
            $table->timestamp('paid_at')->nullable()->index();
            $table->timestamp('receipt_printed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->string('manifest_no')->unique();
            $table->foreignId('origin_branch_id')->constrained('branches');
            $table->foreignId('destination_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('driver_name')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('status', 24)->default('draft')->index();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('manifest_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manifest_id')->constrained('manifests')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('loaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();

            $table->unique(['manifest_id', 'package_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifest_package');
        Schema::dropIfExists('manifests');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('packages');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });

        Schema::dropIfExists('branches');
    }
};
