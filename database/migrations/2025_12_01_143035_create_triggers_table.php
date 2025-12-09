<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER events_insert
            AFTER INSERT ON events
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("events", NEW.id, NEW.created_by, "INSERT", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER events_update
            AFTER UPDATE ON events
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("events", NEW.id, NEW.created_by, "UPDATE", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER events_delete
            AFTER DELETE ON events
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("events", OLD.id, OLD.created_by, "DELETE", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER announcements_insert
            AFTER INSERT ON announcements
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("announcements", NEW.id, NEW.created_by, "INSERT", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER announcements_update
            AFTER UPDATE ON announcements
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("announcements", NEW.id, NEW.created_by, "UPDATE", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER announcements_delete
            AFTER DELETE ON announcements
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("announcements", OLD.id, OLD.created_by, "DELETE", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER organizations_insert
            AFTER INSERT ON organizations
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("organizations", NEW.id, NULL, "INSERT", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER organizations_update
            AFTER UPDATE ON organizations
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("organizations", NEW.id, NULL, "UPDATE", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER organizations_delete
            AFTER DELETE ON organizations
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("organizations", OLD.id, NULL, "DELETE", NOW(), NOW());
            END
        ');

        // USERS table triggers
        DB::unprepared('
            CREATE TRIGGER users_insert
            AFTER INSERT ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("users", NEW.id, NULL, "INSERT", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER users_update
            AFTER UPDATE ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("users", NEW.id, NULL, "UPDATE", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER users_delete
            AFTER DELETE ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("users", OLD.id, NULL, "DELETE", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER departments_insert
            AFTER INSERT ON departments
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("departments", NEW.id, NULL, "INSERT", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER departments_update
            AFTER UPDATE ON departments
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("departments", NEW.id, NULL, "UPDATE", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER departments_delete
            AFTER DELETE ON departments
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("departments", OLD.id, NULL, "DELETE", NOW(), NOW());
            END
        ');

        // LOCATIONS table triggers
        DB::unprepared('
            CREATE TRIGGER locations_insert
            AFTER INSERT ON locations
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("locations", NEW.id, NULL, "INSERT", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER locations_update
            AFTER UPDATE ON locations
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("locations", NEW.id, NULL, "UPDATE", NOW(), NOW());
            END
        ');

        DB::unprepared('
            CREATE TRIGGER locations_delete
            AFTER DELETE ON locations
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, user_id, action, created_at, updated_at)
                VALUES ("locations", OLD.id, NULL, "DELETE", NOW(), NOW());
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS events_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS events_update');
        DB::unprepared('DROP TRIGGER IF EXISTS events_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS announcements_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS announcements_update');
        DB::unprepared('DROP TRIGGER IF EXISTS announcements_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS organizations_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS organizations_update');
        DB::unprepared('DROP TRIGGER IF EXISTS organizations_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS users_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS users_update');
        DB::unprepared('DROP TRIGGER IF EXISTS users_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS departments_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS departments_update');
        DB::unprepared('DROP TRIGGER IF EXISTS departments_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS locations_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS locations_update');
        DB::unprepared('DROP TRIGGER IF EXISTS locations_delete');
    }
};
