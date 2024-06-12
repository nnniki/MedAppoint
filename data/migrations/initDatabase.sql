-- Add sample data for patients

INSERT INTO `patients`(`username`, `password`, `email`, `first_name`, `last_name`, `egn`, `address`, `phone_number`) VALUES
("nkostandie", "6038a086464502c0b30a2fe89490348dee2ef020", "nkostandie@uni-sofia.bg", "Николай", "Костандиев", "1234567890", "София, жк Студентски град", '0888123411'),
("ivn-maria", "561acdc636ece0176a960dd4162192c9939bbe0c", "ivn.maria@gmail.com", "Мария", "Иванова", "0212345670", "София, жк Лозенец", '0878123456');

-- Add sample data for doctors
INSERT INTO `doctors` (`username`, `password`, `email`, `first_name`, `last_name`, `egn`, `work_address`, `region`, `phone_number`, `speciality`) VALUES
('pesho', '6ed95172cef6020ab37165a7dce8f505c8659683', 'petar_petrov@gmail.com', 'Петър', 'Петров', '1234512345', 'София, жк Манастирски Ливади', 'София', '0884871219', 'кардиолог'),
('niki', '6038a086464502c0b30a2fe89490348dee2ef020', 'nikola_stoqnow@gmail.com', 'Никола', 'Стоянов', '1234512345', 'Пловдив, жк Капана', 'Пловдив', '0884871234', 'УНГ'),
('mitko', '1e9d42b13721ae13022daaad335e40e13eb1c639', 'mitko_mitkov@abv.bg', 'Димитър', 'Димитров', '1234512345', 'Бургас, жк Меден Рудник', 'Бургас', '0884871777', 'невролог');

-- Add sample data for appointments
INSERT INTO `appointments` (`doctor_id`, `patient_id`, `review`, `rating`, `notes`, `location`, `appointment_date`) VALUES
( 1, 1, 'Много добро отношение. Препоръчвам!', 5, 'Моля, донесете направените медицински снимки!', 'кабинет 123', '2024-12-06 10:00:00'),
( 2, 1, 'Докторът е голям специалист, Без забележки.', 4, 'Направете предварителни изследвания на кръв и урина', 'кабинет 123', '2024-10-06 11:30:00'),
( 3, 2, 'Доста изчерпателен и подробен преглед!', 4, 'Донесете информация за съпътстващи заболявания', 'кабинет 235', '2024-10-06 14:00:00'),
( 1, 1, NULL, NULL, 'Моля, изпратете информация от предишни лечения.', 'кабинет 235', '2024-03-03 10:00:00'),
( 1, 1, NULL, NULL, NULL, 'кабинет 123', '2024-06-15 08:00:00'),
( 1, NULL, NULL, NULL, NULL, 'кабинет 123', '2024-06-15 09:30:00'),
( 1, NULL, NULL, NULL, NULL, 'кабинет 666', '2024-06-17 08:00:00'),
( 1, NULL, NULL, NULL, NULL, 'кабинет 666', '2024-06-17 09:00:00');
