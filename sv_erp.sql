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

