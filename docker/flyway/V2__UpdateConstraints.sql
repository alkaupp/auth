ALTER TABLE user_applications
    DROP CONSTRAINT user_applications_user_id,
    ADD CONSTRAINT user_applications_user_id
        FOREIGN KEY (user_id) REFERENCES "user" (id)
            ON DELETE CASCADE;
ALTER TABLE user_applications
    DROP CONSTRAINT user_applications_application_id,
    ADD CONSTRAINT user_applications_application_id
        FOREIGN KEY (application_id) REFERENCES application (id)
            ON DELETE CASCADE;
