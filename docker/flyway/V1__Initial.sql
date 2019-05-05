CREATE TABLE application (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    site VARCHAR(255) NOT NULL,
    secretKey VARCHAR(255) NOT NULL
);
CREATE TABLE "user" (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    UNIQUE (email)
);
CREATE TABLE user_applications (
    user_id CHAR(36) NOT NULL,
    application_id CHAR(36) NOT NULL,
    CONSTRAINT user_applications_user_id FOREIGN KEY (user_id) REFERENCES "user" (id),
    CONSTRAINT user_applications_application_id FOREIGN KEY (application_id) REFERENCES application (id)
);