CREATE TABLE auth.application (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    site VARCHAR(255) NOT NULL,
    secretKey VARCHAR(255) NOT NULL
);
CREATE TABLE auth.user (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    app_id CHAR(36) NOT NULL,
    UNIQUE (email),
    CONSTRAINT user_appId FOREIGN KEY (app_id) REFERENCES auth.application (id)
);