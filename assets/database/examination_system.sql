CREATE DATABASE exam_system;

use exam_system;

CREATE TABLE department (
D_ID varchar(10) NOT NULL,
D_Name varchar(40) NOT NULL,
No_of_Employees varchar(3) NOT NULL,
constraint department_pk primary key (D_ID)
);

CREATE TABLE exam_candidate (
C_ID varchar(10) NOT NULL,
F_Name varchar(25) NOT NULL,
L_Name varchar(25) NOT NULL,
D_ID varchar(10) NOT NULL,
DOB date NOT NULL,
NIC varchar(12) NOT NULL,
Email varchar(40) NOT NULL,
Age varchar(2) NOT NULL,
Gender varchar(10) NOT NULL,
Password varchar(15) NOT NULL,
constraint exam_candidate_pk primary key (C_ID),
constraint exam_candidate_fk foreign key (D_ID) references department(D_ID) on delete cascade
);

CREATE TABLE staff (
S_ID varchar(10) NOT NULL,
F_Name varchar(25) NOT NULL,
L_Name varchar(25) NOT NULL,
D_ID varchar(10) NOT NULL,
DOB date NOT NULL,
NIC varchar(12) NOT NULL,
Email varchar(40)  NOT NULL,
Gender varchar(10) NOT NULL,
Age varchar(2) NOT NULL,
Role varchar(15) NOT NULL,
Password varchar(15) NOT NULL,
constraint staff_pk primary key (S_ID),
constraint staff_fk foreign key (D_ID) references department(D_ID) on delete cascade
);

CREATE TABLE exam (
E_ID varchar(10) NOT NULL,
E_Name varchar(50) NOT NULL,
Q_password varchar(25) NOT NULL,
Duration time NOT NULL,
S_ID varchar(10) NOT NULL,
constraint exam_pk primary key (E_ID),
constraint exam_fk foreign key (S_ID) references staff(S_ID) on delete cascade
);

CREATE TABLE attends (
E_ID varchar(10) NOT NULL,
C_ID varchar(10) NOT NULL,
Result decimal(5,2) NOT NULL,
constraint attends_fk_1 foreign key (C_ID) references exam_candidate(C_ID) on delete cascade,
constraint attends_fk_2 foreign key (E_ID) references exam(E_ID) on delete cascade
);

CREATE TABLE complaint (
C_No varchar(10) NOT NULL,
C_ID varchar(10) NOT NULL,
C_Date date NOT NULL,
C_Title varchar(40) NOT NULL,
C_Details varchar(255) NOT NULL,
constraint complaint_pk primary key (C_No),
constraint complaint_fk_1 foreign key (C_ID) references exam_candidate(C_ID) on delete cascade
);

CREATE TABLE feedback (
Feedback_ID varchar(10) NOT NULL,
C_ID varchar(10) NOT NULL,
Date date NOT NULL,
F_Details varchar(255) NOT NULL,
constraint feedback_pk primary key (Feedback_ID),
constraint feedback_fk_1 foreign key (C_ID) references exam_candidate(C_ID) on delete cascade
);

CREATE TABLE report (
Report_ID varchar(10) NOT NULL,
R_Date date NOT NULL,
Details varchar(255) NOT NULL,
S_ID varchar(10) NOT NULL,
constraint report_pk primary key (Report_ID),
constraint report_fk foreign key (S_ID) references staff(S_ID) on delete cascade
);

CREATE TABLE question (
Q_ID VARCHAR(10) NOT NULL ,
E_ID VARCHAR(10) NOT NULL ,
Q_content VARCHAR(255) NOT NULL ,
Answer VARCHAR(255) NOT NULL ,
constraint question_pk primary key (Q_ID),
constraint question_fk foreign key (E_ID) references exam(E_ID) on delete cascade
);

CREATE TABLE Staff_views_feedback (
Feedback_ID VARCHAR(10) NOT NULL ,
S_ID VARCHAR(10) NOT NULL ,
constraint Staff_views_feedback_pk primary key (S_ID,Feedback_ID)
);

/*table for exam_candidate_phone_no*/
CREATE TABLE exam_candidate_phone_no (
C_ID varchar(10) NOT NULL,
Phone_no varchar(10) NOT NULL,
constraint exam_candidate_phone_no_pk primary key (C_ID,Phone_no)
);

/*table for staff_phone_no*/
CREATE TABLE staff_phone_no (
S_ID varchar(10) NOT NULL,
S_phone_no varchar(10) NOT NULL,
constraint staff_phone_no_pk primary key (S_ID,S_phone_no)
);

INSERT INTO department (D_ID, D_Name,No_of_Employees) 
VALUES 
('D001', 'Human Resources', 25),
('D002', 'Finance', 30),
('D003', 'IT', 45),
('D004', 'Marketing', 20),
('D005', 'Sales', 35);

INSERT INTO exam_candidate (C_ID, F_Name, L_Name, D_ID, DOB, NIC, Email, Age, Gender, Password) 
VALUES
('C001', 'John', 'Doe', 'D001', '1995-05-15', '123456789V', 'john.doe@gmail.com', 29, 'Male','password1234'),
('C002', 'Jane', 'Smith', 'D002', '1998-08-22', '987654321V', 'jane.smith@gmail.com', 26, 'Female','password1564'),
('C003', 'Michael', 'Brown', 'D003', '1992-11-30', '456789123V', 'michael.brown@gmail.com', 31, 'Male','password1784'),
('C004', 'Emily', 'Davis', 'D004', '2000-02-14', '321654987V', 'emily.davis@gmail.com', 24, 'Female','password1894'),
('C005', 'David', 'Wilson', 'D005', '1997-07-07', '789123456V', 'david.wilson@gmail.com', 27, 'Male','password4564');

INSERT INTO staff (S_ID, F_Name, L_Name, D_ID, DOB, NIC, Email, Gender, Age, Role, Password) 
VALUES
('S001', 'John', 'Doe', 'D001', '1985-05-15', '123456789V', 'johmn@gmail.com', 'Male', 39, 'Manager','password1474'),
('S002', 'Jane', 'Smith', 'D001', '1990-08-22', '987654321V', 'jsvol@gmail.com', 'Female', 34, 'Examiner','password1954'),
('S003', 'Emily', 'Johnson', 'D002', '1988-12-10', '456789123V', 'kanbachb@gmail.com', 'Female', 35, 'Examiner','password1904'),
('S004', 'Michael', 'Brown', 'D004', '1992-03-05', '789123456V', 'bshbshj@gmail.com', 'Male', 32, 'Admin','password1674'),
('S005', 'Sarah', 'Davis', 'D003', '1987-07-19', '321654987V', 'staff@gmail.com', 'Female', 37, 'Examiner','password2344');

INSERT INTO exam (E_ID, E_Name , Q_password, Duration, S_ID) 
VALUES 
('E001', 'Exam 1' , 'pass123', '01:30:00', 'S001'),
('E002', 'Exam 2' ,'secure456', '02:00:00', 'S002'),
('E003', 'Exam 3' ,'exam789', '01:45:00', 'S003'),
('E004', 'Exam 4' ,'test321', '02:30:00', 'S004'),
('E005', 'Exam 5' ,'quiz654', '01:00:00', 'S005'); 

INSERT INTO attends (E_ID,C_ID,Result) 
VALUES 
('E001', 'C001', 85.50),
('E002', 'C002', 90.75),
('E003', 'C003', 78.25),
('E004', 'C004', 88.00),
('E005', 'C005', 92.50);
INSERT INTO report (Report_ID, R_Date, Details, S_ID) 
VALUES
('R001', '2024-09-01', 'Monthly sales report', 'S001'),
('R002', '2024-09-15', 'Quarterly performance review', 'S002'),
('R003', '2024-09-20', 'Annual financial summary', 'S003');
INSERT INTO feedback (Feedback_ID, C_ID, Date, F_Details)
VALUES 
('F001', 'C001', '2024-09-29', 'Very helpful during the exam process'),
('F002', 'C002', '2024-09-28', 'Explained the exam format clearly'),
('F003', 'C003', '2024-09-27', 'Provided excellent guidance on the procedures'),
('F004', 'C004', '2024-09-26', 'Was available for support when needed'),
('F005', 'C005', '2024-09-25', 'Gave thorough and clear instructions');
INSERT INTO complaint (C_No, C_ID, C_Date, C_Title, C_Details)
VALUES 
('CMP001', 'C001', '2024-09-29', 'Late Exam Start', 'The exam started 30 minutes late without prior notice.'),
('CMP002', 'C002', '2024-09-28', 'Unclear Instructions', 'The staff did not provide clear instructions for the exam.'),
('CMP003', 'C003', '2024-09-27', 'Technical Issues', 'There were multiple technical issues during the exam process.'),
('CMP004', 'C004', '2024-09-26', 'Inadequate Support', 'Support was insufficient during the exam registration process.'),
('CMP005', 'C005', '2024-09-25', 'Unprofessional Behavior', 'The staff was unprofessional and rude during the exam.');
INSERT INTO exam_candidate_phone_no (C_ID, Phone_no) 
VALUES 
('C001', 9876543210),
('C002', 8765432109),
('C003', 7654321098),
('C004', 6543210987),
('C005', 5432109876);
INSERT INTO staff_phone_no (S_ID, S_phone_no) 
VALUES 
('S001', 1234567890),
('S002', 2345678901),
('S003', 3456789012),
('S004', 4567890123),
('S005', 5678901234);

INSERT INTO question (`Q_ID`, `E_ID`, `Q_content`, `Answer`)
VALUES 
('Q001', 'E001', 'What does HTML stand for?', 'HyperText Markup Language'),
('Q002', 'E002', 'Who is known as the father of the computer?', 'Charles Babbage'),
('Q003', 'E003', 'What is the time complexity of binary search?', 'O(log n)'),
('Q004', 'E004', 'Which programming language is known as the "mother of all languages"?', 'Assembly Language'),
('Q005', 'E005', 'What does SQL stand for?', 'Structured Query Language');