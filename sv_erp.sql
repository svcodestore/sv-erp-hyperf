drop schema if exists sv_erp;
create schema sv_erp;
use sv_erp;

show tables;

CREATE TABLE `prod_schedule_calendar`
(
    `id`         int                                                      NOT NULL AUTO_INCREMENT,
    `date`       date                                                     NOT NULL,
    `year`       char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `month`      char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `day`        char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `is_rest`    tinyint(1)                                               NOT NULL DEFAULT 0,
    `profile`    json                                                              DEFAULT NULL,
    `created_by` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `updated_by` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `created_at` timestamp                                                NULL     DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp                                                NULL     DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT prod_schedule_calendar_rest CHECK ( is_rest = 1 OR is_rest = 0 ),
    UNIQUE KEY `prod_schedule_calendar_date` (`date`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `prod_schedule_params`
(
    `key`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `value`  varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    UNIQUE KEY `prod_schedule_params_key` (`key`) USING BTREE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `prod_schedule_po`
(
    `id`             int                                                          NOT NULL AUTO_INCREMENT,
    `workshop`       char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL COMMENT '工作站台',
    `workshop_name`  varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `customer_no`    varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '客户号',
    `customer_po_no` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '客户订单号',
    `item_code`      varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '生产款号',
    `item_qty`       int                                                               DEFAULT NULL COMMENT '生产数量',
    `po_month`       tinyint(2)                                                        DEFAULT NULL,
    `po_year`        smallint(4)                                                       DEFAULT NULL,
    `is_dirty`       tinyint(1)                                                        DEFAULT 0 COMMENT '是否是外部数据，导入数据，0为否',
    `created_by`     varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `updated_by`     varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `created_at`     timestamp                                                    NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     timestamp                                                    NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `prod_schedule_phase`
(
    `id`         int        NOT NULL AUTO_INCREMENT,
    `code`       varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `code_id`    varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `name`       varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `cost_time`  int                                                          DEFAULT NULL,
    `is_master`  tinyint(1) NOT NULL                                          DEFAULT 1,
    `ahead_time` int                                                          DEFAULT NULL,
    `dead_time`  int                                                          DEFAULT NULL,
    `out_time`   int                                                          DEFAULT NULL,
    `worker_num` tinyint(1)                                                   DEFAULT 1 COMMENT '工站作业人员系数数量；',
    `is_dirty`   tinyint(1)                                                   DEFAULT 1,
    `created_by` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `updated_by` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp  NULL                                              DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp  NULL                                              DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

insert into prod_schedule_params(`key`, value, remark)
VALUES ('bisection_count', '18', '生产数等分数量');
insert into prod_schedule_params(`key`, value, remark)
VALUES ('shifts',
        '[{\"name\":\"白班\",\"times\":[{\"name\":\"上午\",\"start\":\"2021-10-06T23:30:00.000Z\",\"end\":\"2021-10-07T03:30:00.000Z\"},{\"name\":\"下午\",\"start\":\"2021-10-07T05:00:00.000Z\",\"end\":\"2021-10-07T09:00:00.000Z\"},{\"name\":\"晚上\",\"start\":\"2021-10-07T09:30:00.000Z\",\"end\":\"2021-10-07T13:30:00.000Z\"}]}]',
        '班次设定');

CREATE TABLE hr_kpi_items
(
    id         int auto_increment,
    pid        int         not null default 0,
    code       varchar(6)  not null,
    name       varchar(255),
    version    varchar(32),
    created_at datetime(6) not null default current_timestamp(6),
    created_by bigint      not null,
    updated_at datetime(6) not null default current_timestamp(6) on update current_timestamp(6),
    updated_by bigint      not null,
    primary key (id),
    index hr_kpi_item_index_version (version),
    index hr_kpi_item_index_pid (pid),
    constraint unique hr_kpi_item_unique_index_code (code)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE hr_kpi_item_category
(
    id         int auto_increment,
    code       varchar(6)  not null,
    name       varchar(255),
    version    varchar(32),
    created_at datetime(6) not null default current_timestamp(6),
    created_by bigint      not null,
    updated_at datetime(6) not null default current_timestamp(6) on update current_timestamp(6),
    updated_by bigint      not null,
    primary key (id),
    index hr_kpi_item_category_index_version (version),
    constraint unique hr_kpi_item_category_unique_index_code (code)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE hr_kpi_titles
(
    id         int auto_increment,
    name       varchar(20) not null,
    version    varchar(32),
    created_at datetime(6) not null default current_timestamp(6),
    created_by bigint      not null,
    updated_at datetime(6) not null default current_timestamp(6) on update current_timestamp(6),
    updated_by bigint      not null,
    primary key (id),
    index hr_kpi_title_index_version (version),
    constraint unique hr_kpi_title_unique_index_name (name)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE hr_kpi_title_category
(
    id         int auto_increment,
    name       varchar(20) not null,
    version    varchar(32),
    created_at datetime(6) not null default current_timestamp(6),
    created_by bigint      not null,
    updated_at datetime(6) not null default current_timestamp(6) on update current_timestamp(6),
    updated_by bigint      not null,
    primary key (id),
    index hr_kpi_title_category_index_version (version)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE hr_kpi_ranks
(
    id         int auto_increment,
    code       varchar(6)  not null,
    version    varchar(32),
    created_at datetime(6) not null default current_timestamp(6),
    created_by bigint      not null,
    updated_at datetime(6) not null default current_timestamp(6) on update current_timestamp(6),
    updated_by bigint      not null,
    primary key (id),
    index hr_kpi_rank_index_version (version),
    constraint unique hr_kpi_rank_unique_index_code (code)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE hr_kpi_position_group
(
    id         int auto_increment,
    pid        int         not null,
    name       varchar(20) not null,
    version    varchar(32),
    created_at datetime(6) not null default current_timestamp(6),
    created_by bigint      not null,
    updated_at datetime(6) not null default current_timestamp(6) on update current_timestamp(6),
    updated_by bigint      not null,
    primary key (id),
    index hr_kpi_position_group_index_version (version),
    index hr_kpi_position_group_index_pid (pid)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE hr_kpi_position_item
(
    id         int auto_increment,
    name       varchar(20) not null,
    version    varchar(32),
    created_at datetime(6) not null default current_timestamp(6),
    created_by bigint      not null,
    updated_at datetime(6) not null default current_timestamp(6) on update current_timestamp(6),
    updated_by bigint      not null,
    primary key (id),
    index hr_kpi_position_item_index_version (version),
    constraint unique hr_kpi_position_item_unique_index_name (name)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE hr_kpi_positions
(
    id            int auto_increment,
    item_id       int              not null,
    group_id      int              not null,
    kpi_id        int              not null,
    category_id   int              not null,
    score_percent tinyint unsigned not null,
    version       varchar(32),
    created_at    datetime(6)      not null default current_timestamp(6),
    created_by    bigint           not null,
    updated_at    datetime(6)      not null default current_timestamp(6) on update current_timestamp(6),
    updated_by    bigint           not null,
    primary key (id),
    index hr_kpi_position_index_version (version),
    index hr_kpi_position_index_item (item_id),
    index hr_kpi_position_index_group (group_id),
    index hr_kpi_position_index_kpi (kpi_id),
    index hr_kpi_position_index_category (category_id),
    constraint unique hr_kpi_position_unique_index (item_id, group_id, category_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

create table hr_kpi_rank_title
(
    id         int auto_increment,
    rank_id    int         not null,
    title_id   int         not null,
    version    varchar(32),
    created_at datetime(6) not null default current_timestamp(6),
    created_by bigint      not null,
    updated_at datetime(6) not null default current_timestamp(6) on update current_timestamp(6),
    updated_by bigint      not null,
    primary key (id),
    constraint unique hr_kpi_rank_title_unique_index (rank_id, title_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

create table hr_kpi_staffs
(
    id                int auto_increment,
    number            int         not null,
    name              varchar(16) not null,
    position_group_id int         not null,
    position_id       int         not null,
    rank_title_id     int         not null,
    employed_in       date        not null,
    created_at        datetime(6) not null default current_timestamp(6),
    created_by        bigint      not null,
    updated_at        datetime(6) not null default current_timestamp(6) on update current_timestamp(6),
    updated_by        bigint      not null,
    primary key (id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


create table hr_kpi_rule_item
(
    id         int auto_increment,
    category   varchar(32)  not null default 'ratio',
    expression varchar(255) not null default '90-0:0.95-0.60,100-90:0.98-0.95',
    remark     varchar(255),
    created_at datetime(6)  not null default current_timestamp(6),
    created_by bigint       not null,
    updated_at datetime(6)  not null default current_timestamp(6) on update current_timestamp(6),
    updated_by bigint       not null,
    primary key (id),
    constraint unique hr_kpi_rule_item_category (category)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

create table hr_kpi_rules
(
    id                int auto_increment,
    kpi_id            int          not null,
    position_group_id int          not null,
    rule_item_id      int          not null,
    rule_expression   varchar(255) not null,
    created_at        datetime(6)  not null default current_timestamp(6),
    created_by        bigint       not null,
    updated_at        datetime(6)  not null default current_timestamp(6) on update current_timestamp(6),
    updated_by        bigint       not null,
    primary key (id),
    constraint unique hr_kpi_rules_unique (kpi_id, position_group_id),
    constraint hr_kpi_rules_fk_kpi_id foreign key (kpi_id) references hr_kpi_items (id) on update cascade on delete cascade,
    constraint hr_kpi_rules_fk_position_group_id foreign key (position_group_id) references hr_kpi_position_group (id) on update cascade on delete cascade,
    constraint hr_kpi_rules_fk_rule_item_id foreign key (rule_item_id) references hr_kpi_rule_item (id) on update cascade on delete cascade
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

create table hr_kpi_position_group_score
(
    id         int auto_increment,
    rule_id    int         not null,
    month      varchar(7)  not null,
    score      varchar(6)  not null,
    kpi_score  varchar(6)  not null,
    created_at datetime(6) not null default current_timestamp(6),
    created_by bigint      not null,
    updated_at datetime(6) not null default current_timestamp(6) on update current_timestamp(6),
    updated_by bigint      not null,
    primary key (id),
    constraint unique hr_kpi_position_group_score_unique (rule_id, month),
    constraint hr_kpi_position_group_score_fk_rule_id foreign key (rule_id) references hr_kpi_rules (id) on update cascade
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

create table hr_kpi_staff_score
(
    id         int auto_increment,
    staff_id   int         not null,
    kpi_id     int         not null,
    score      varchar(6)  not null,
    kpi_score  varchar(6)  not null,
    month      varchar(7)  not null,
    created_at datetime(6) not null default current_timestamp(6),
    created_by bigint      not null,
    updated_at datetime(6) not null default current_timestamp(6) on update current_timestamp(6),
    updated_by bigint      not null,
    primary key (id),
    constraint unique hr_kpi_staff_score_unique (staff_id, kpi_id),
    constraint hr_kpi_staff_score_fk_staff_id foreign key (staff_id) references hr_kpi_staffs (id) on update cascade on delete cascade,
    constraint hr_kpi_staff_score_fk_kpi_id foreign key (kpi_id) references hr_kpi_items (id) on update cascade on delete cascade
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
