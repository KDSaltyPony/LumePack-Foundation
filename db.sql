create table "users" (
    "id" bigserial primary key not null,
    "login" varchar(255) not null,
    "email" varchar(255) not null,
    "password" varchar(255) null,
    "is_active" boolean not null default '1',
    "remember_token" varchar(100) null,
    "email_token" varchar(255) null,
    "pwd_token" varchar(255) null,
    "pwd_token_created_at" timestamp(0) without time zone null,
    "email_verified_at" timestamp(0) without time zone null,
    "created_at" timestamp(0) without time zone null,
    "updated_at" timestamp(0) without time zone null,
    "deleted_at" timestamp(0) without time zone null
);
alter table "users" add constraint "users_login_unique" unique ("login");

create table "user_access_tokens" (
    "id" bigserial primary key not null,
    "tokenable_type" varchar(255) not null,
    "tokenable_id" bigint not null,
    "name" varchar(255) not null,
    "token" varchar(64) not null,
    "abilities" text null,
    "last_used_at" timestamp(0) without time zone null,
    "expires_at" timestamp(0) without time zone null,
    "created_at" timestamp(0) without time zone null,
    "updated_at" timestamp(0) without time zone null
);
create index "user_access_tokens_tokenable_type_tokenable_id_index" on "user_access_tokens" ("tokenable_type", "tokenable_id");
alter table "user_access_tokens" add constraint "user_access_tokens_token_unique" unique ("token");

create table "roles" (
    "id" bigserial primary key not null,
    "uid" varchar(255) not null,
    "name" varchar(255) not null,
    "created_at" timestamp(0) without time zone null,
    "updated_at" timestamp(0) without time zone null,
    "deleted_at" timestamp(0) without time zone null
);
alter table "roles" add constraint "roles_uid_unique" unique ("uid");

create table "permission_types" (
    "id" bigserial primary key not null,
    "uid" varchar(255) not null,
    "name" varchar(255) not null,
    "created_at" timestamp(0) without time zone null,
    "updated_at" timestamp(0) without time zone null,
    "deleted_at" timestamp(0) without time zone null
);
alter table "permission_types" add constraint "permission_types_uid_unique" unique ("uid");

create table "permissions" (
    "id" bigserial primary key not null,
    "uid" varchar(255) not null,
    "name" varchar(255) not null,
    "permission_type_id" bigint not null,
    "created_at" timestamp(0) without time zone null,
    "updated_at" timestamp(0) without time zone null,
    "deleted_at" timestamp(0) without time zone null
);
alter table "permissions" add constraint "permissions_permission_type_id_foreign" foreign key ("permission_type_id") references "permission_types" ("id") on delete cascade;
alter table "permissions" add constraint "permissions_uid_unique" unique ("uid");

create table "user_role" (
    "user_id" bigint not null,
    "role_id" bigint not null
);
alter table "user_role" add constraint "user_role_user_id_foreign" foreign key ("user_id") references "users" ("id") on delete cascade;
alter table "user_role" add constraint "user_role_role_id_foreign" foreign key ("role_id") references "roles" ("id") on delete cascade;
alter table "user_role" add primary key ("user_id", "role_id");

create table "role_permission" (
    "role_id" bigint not null,
    "permission_id" bigint not null
);
alter table "role_permission" add constraint "role_permission_role_id_foreign" foreign key ("role_id") references "roles" ("id") on delete cascade;
alter table "role_permission" add constraint "role_permission_permission_id_foreign" foreign key ("permission_id") references "permissions" ("id") on delete cascade;
alter table "role_permission" add primary key ("role_id", "permission_id");

create table "media" (
    "id" bigserial primary key not null,
    "uid" varchar(255) not null,
    "name" varchar(255) not null,
    "comments" varchar(255) null,
    "max_chunk" integer not null,
    "mimetype" varchar(255) not null,
    "min_width" smallint null,
    "max_width" smallint null,
    "min_height" smallint null,
    "max_height" smallint null,
    "created_at" timestamp(0) without time zone null,
    "updated_at" timestamp(0) without time zone null,
    "deleted_at" timestamp(0) without time zone null
);
alter table "media" add constraint "media_uid_unique" unique ("uid");

create table "files" (
    "id" bigserial primary key not null,
    "name" varchar(255) not null,
    "token" varchar(255) not null,
    "extension" varchar(255) not null,
    "size" integer not null,
    "media_id" bigint not null,
    "created_at" timestamp(0) without time zone null,
    "updated_at" timestamp(0) without time zone null,
    "deleted_at" timestamp(0) without time zone null
);
alter table "files" add constraint "files_media_id_foreign" foreign key ("media_id") references "media" ("id") on delete cascade;

create table "taxonomies" (
    "id" bigserial primary key not null,
    "uid" varchar(255) not null,
    "name" varchar(255) not null,
    "is_ordered" boolean not null default '0',
    "created_at" timestamp(0) without time zone null,
    "updated_at" timestamp(0) without time zone null,
    "deleted_at" timestamp(0) without time zone null
);
alter table "taxonomies" add constraint "taxonomies_uid_unique" unique ("uid");

create table "taxonomy_values" (
    "id" bigserial primary key not null,
    "uid" varchar(255) not null,
    "value" varchar(255) not null,
    "order" integer null,
    "taxonomy_id" bigint not null,
    "created_at" timestamp(0) without time zone null,
    "updated_at" timestamp(0) without time zone null,
    "deleted_at" timestamp(0) without time zone null
);
alter table "taxonomy_values" add constraint "taxonomy_values_taxonomy_id_foreign" foreign key ("taxonomy_id") references "taxonomies" ("id") on delete cascade;
alter table "taxonomy_values" add constraint "taxonomy_values_uid_unique" unique ("uid");

create table "dbversions" (
    "id" bigserial primary key not null,
    "version" varchar(255) not null,
    "sqlscript" text not null, "comments" text null,
    "created_at" timestamp(0) without time zone null,
    "updated_at" timestamp(0) without time zone null,
    "deleted_at" timestamp(0) without time zone null
);
alter table "dbversions" add constraint "dbversions_version_unique" unique ("version");

create table "sendmails" (
    "id" bigserial primary key not null,
    "token" varchar(255) not null,
    "from" varchar(255) not null,
    "to" varchar(255) not null,
    "subject" varchar(255) not null,
    "content" text not null,
    "sent_at" timestamp(0) without time zone not null,
    "is_success" boolean null,
    "created_at" timestamp(0) without time zone null,
    "updated_at" timestamp(0) without time zone null,
    "deleted_at" timestamp(0) without time zone null
);

create table "crontasks" (
    "id" bigserial primary key not null,
    "uid" varchar(255) not null,
    "command" varchar(255) not null,
    "minute" char(255) not null default '*',
    "hour" char(255) not null default '*',
    "day" char(255) not null default '*',
    "month" char(255) not null default '*',
    "year" char(255) not null default '*',
    "created_at" timestamp(0) without time zone null,
    "updated_at" timestamp(0) without time zone null,
    "deleted_at" timestamp(0) without time zone null
);
alter table "crontasks" add constraint "crontasks_uid_unique" unique ("uid");

insert into "permission_types" ("uid", "name") values ("ENDPOINT", 'Endpoint');
insert into "permission_types" ("uid", "name") values ("MODEL_ATTR", 'Model attribute');
