<?php
    require_once root_dir . "/models/student.php";
    require_once root_dir . "/app/controllers/BaseController.php";

    class StudentController extends BaseController {
        private StudentRepository $studentRepo;

        public function __construct() {
            $this->studentRepo = new StudentRepository();
        }

        
    }
?>