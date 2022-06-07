create table if not exists `users`
(
    `id`            int auto_increment
        primary key,
    `username`      varchar(255)                         not null,
    `email`         varchar(255)                         not null,
    `password`      varchar(255)                         not null,
    `roles`         json                                 null,
    `registered_at` datetime   default CURRENT_TIMESTAMP null,
    `active`        tinyint(1) default 0                 not null,
    `token`         varchar(255)                         null,
    constraint users_email_uindex
        unique (`email`),
    constraint users_token_uindex
        unique (`token`),
    constraint users_username_uindex
        unique (`username`)
);

create table if not exists `posts`
(
    `id`         int auto_increment
        primary key,
    `author`     int                                not null,
    `slug`       varchar(255)                       not null,
    `title`      varchar(255)                       not null,
    `lede`       text                               not null,
    `content`    longtext                           not null,
    `created_at` datetime default CURRENT_TIMESTAMP null,
    `updated_at` datetime default CURRENT_TIMESTAMP not null,
    constraint posts_slug_uindex
        unique (`slug`),
    constraint posts_users_id_fk
        foreign key (`author`) references `users` (`id`)
            on update cascade
);

create table if not exists `comments`
(
    `id`         int auto_increment
        primary key,
    `author`     int                                  not null,
    `post`       int                                  not null,
    `content`    longtext                             not null,
    `created_at` datetime   default CURRENT_TIMESTAMP not null,
    `valid`      tinyint(1) default 0                 null,
    constraint comments_posts_id_fk
        foreign key (`post`) references `posts` (`id`)
            on update cascade,
    constraint comments_users_id_fk
        foreign key (`author`) references `users` (`id`)
            on update cascade
);
