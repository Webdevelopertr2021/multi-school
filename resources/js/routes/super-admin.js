import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

// Components
import AddSchool from "../components/SuperAdmin/schools/AddSchool.vue";
import SchoolList from "../components/SuperAdmin/schools/SchoolList.vue";
import SchoolDetails from "../components/SuperAdmin/schools/SchoolDetails.vue";
import AddSuperVisor from "../components/SuperAdmin/schools/AddSuperVisor.vue";
import AddTeacher from "../components/SuperAdmin/schools/AddTeacher.vue";
import SupervisorList from "../components/SuperAdmin/supervisor/List.vue";
import EditSupervisor from "../components/SuperAdmin/supervisor/Edit.vue";
import NotificationList from "../components/NotifcationList.vue";
import CreateClass from "../components/SuperAdmin/class/CreateClass.vue";
import CreateSection from "../components/SuperAdmin/class/CreateSection.vue";
import EditSection from "../components/SuperAdmin/class/EditSection.vue";
import CreateStudent from "../components/SuperAdmin/student/Create.vue";
import EditStudent from "../components/SuperAdmin/student/Edit.vue";
import Listtudent from "../components/SuperAdmin/student/List.vue";
import ImportStudent from "../components/SuperAdmin/student/Import.vue";
import StudentRatings from "../components/SuperAdmin/student/StudentRatings.vue";
import AllTeacherList from "../components/SuperAdmin/teacher/TeacherList.vue";
import EditTeacher from "../components/SuperAdmin/teacher/EditTeacher.vue";
import AllAdmin from "../components/SuperAdmin/admins/List.vue";
import AllManger from "../components/SuperAdmin/admins/ManagerList.vue";
import AdminDashboard from "../components/SuperAdmin/home/Dashboard.vue";
import CreateAdmin from "../components/SuperAdmin/admins/Create.vue";
import CreateManager from "../components/SuperAdmin/admins/CreateManager.vue";
import EditAdmin from "../components/SuperAdmin/admins/Edit.vue";
import EditManager from "../components/SuperAdmin/admins/EditManager.vue";
import MyProfile from "../components/SuperAdmin/admins/MyProfile.vue";
import TeacherRatings from "../components/SuperAdmin/teacher/AllRatings.vue";
import LeaveRequest from "../components/SuperAdmin/LeaveRequest/List.vue";
import StudentListByTeacher from "../components/SuperAdmin/teacher/StudentListByTeacher.vue";
import TeacherListSuperv from "../components/SuperAdmin/supervisor/Teachers.vue";
import PaySalary from "../components/SuperAdmin/payments/PaySalary.vue";
import PaymentList from "../components/SuperAdmin/payments/PaymentList.vue";
import StaffTree from "../components/SuperAdmin/admins/StaffTree.vue";
import Settings from "../components/SuperAdmin/admins/Settings.vue";
import ExamList from "../components/SuperAdmin/exam/ExamList.vue";
import Questions from "../components/SuperAdmin/exam/Questions.vue";
import EditExam from "../components/SuperAdmin/exam/EditExam.vue";
// End

// Exams
import AddExam from "../components/SuperAdmin/exam/CreateExam.vue";
// end

const prefix = "/admin/"
const routes = new VueRouter({
    mode: "history",
    linkExactActiveClass: "active",
    linkActiveClass: "active",
    routes: [
        {
            path: prefix + "dashboard",
            component: AdminDashboard,
            name: 'admin.dashboard',
            meta: {
                title: "Admin Dashboard"
            }
        },
        // School
        {
            path: prefix + "add-school",
            component: AddSchool,
            name: "admin.add-school",
            meta: {
                title: "Add new school"
            }
        },
        {
            path: prefix+ "school-list",
            component: SchoolList,
            name: "admin.school-list",
            meta: {
                title : "All schools"
            }
        },
        {
            path: prefix+ "school-details/:schoolId",
            component: SchoolDetails,
            name: "admin.school-details",
            meta: {
                title: "School details"
            }
        },
        {
            path: prefix + "add-supervisor",
            name: "admin.add-supervisor",
            component: AddSuperVisor,
            meta: {
                title : "Add supervisors"
            }
        },
        {
            path: prefix + "add-teacher/school/:schoolId",
            name: "admin.add-teacher",
            component: AddTeacher,
            meta: {
                title : "Add teacher"
            }
        },
        {
            path: prefix + "supervisor-list",
            name: "admin.supervisor-list",
            component: SupervisorList,
            meta: {
                title : "Supervisor list"
            }
        },
        {
            path : prefix + "edit-supervisor-profile/user/:userId",
            name: "admin.edit-superv",
            component: EditSupervisor,
            meta : {
                title : "Edit supervisor"
            }
        },
        {
            path: prefix + "notifications",
            name: "notification",
            component: NotificationList,
            meta: {
                title : "My notifications"
            }
        },
        {
            path: prefix + "create-class",
            name: "admin.create-class",
            component: CreateClass,
            meta: {
                title: "Create class"
            }
        },
        {
            path: prefix + "create-section",
            name: "admin.create-section",
            component: CreateSection,
            meta: {
                title: "Create section"
            }
        },
        {
            path: prefix + "edit-section/:sectionId",
            name: "admin.edit-section",
            component: EditSection,
            meta : {
                title: "Edit section"
            }
        },
        {
            path: prefix + "add-students",
            name: "admin.add-student",
            component:CreateStudent,
            meta: {
                title: "Add Student"
            }
        },
        {
            path: prefix + "student-list",
            name: "admin.student-list",
            component: Listtudent,
            meta: {
                title : "Student List"
            }
        },
        {
            path: prefix + "edit-student/:studentId",
            name: "admin.edit-student",
            component: EditStudent,
            meta : {
                title : "Edit student list"
            }
        },
        {
            path: prefix + "import-student",
            name: "admin.import-student",
            component: ImportStudent,
            meta: {
                title: "Import students"
            }
        },
        {
            path: prefix + "student-ratings/:studentId",
            name: "admin.student-rating",
            component: StudentRatings,
            meta: {
                title: "Student ratings"
            }
        },
        {
            path: prefix + "all-teachers",
            name: "admin.all-teacher",
            component: AllTeacherList,
            meta: {
                title: "All Teachers"
            }
        },
        {
            path: prefix + "edit-teacher-profile/:teacherId",
            name: "admin.teacher-edit",
            component: EditTeacher,
            meta : {
                title: "Teacher Edit"
            }
        },
        {
            path: prefix + "teacher-ratings/:teacherId",
            name: "admin.teacher-ratings",
            component: TeacherRatings,
            meta: {
                title: "Teacher Rating"
            }
        },
        {
            path: prefix + "super-admin-list",
            name: "admin.admin-list",
            component: AllAdmin,
            meta: {
                title : "Admin List"
            }
        },
        {
            path: prefix + "create-admin",
            name: "admin.create",
            component: CreateAdmin,
            meta: {
                title: "Create new admin"
            }
        },
        {
            path: prefix + "edit-admin-profile/:adminId",
            name: "admin.edit",
            component: EditAdmin,
            meta: {
                title: "Edit admin profile",
            }
        },
        {
            path: prefix + "my-profile",
            name: "admin.my-profile",
            component: MyProfile,
            meta: {
                title : "My profile"
            }
        },
        {
            path: prefix+ "leave-request",
            name: "admin.leave-request",
            component:LeaveRequest,
            meta : {
                title: "Leave requests"
            }
        },
        {
            path: prefix + "teacher/:teacherId/studetn-list",
            name: "admin.student-by-teacher",
            component: StudentListByTeacher,
            meta: {
                title : "Student List"
            }
        },
        {
            path: prefix + "supervisor/:supervisorId/teachers",
            name: "admin.superv.teachers",
            component: TeacherListSuperv,
            meta : {
                title : "Supervisor List"
            }
        },
        {
            path: prefix + "manager-list",
            name: "admin.manager-list",
            component: AllManger,
            meta: {
                title : "Manager List"
            }
        },
        {
            path: prefix + "create-manager",
            name: "admin.create-manager",
            component: CreateManager,
            meta: {
                title: "Create manager"
            }
        },
        {
            path: prefix + "edit-manager-profile/:adminId",
            name: "admin.edit-manager",
            component: EditManager,
            meta: {
                title: "Edit manager"
            }
        },
        {
            path: prefix + "pay-salary/:userId",
            name: "admin.pay-salary",
            component: PaySalary,
            meta: {
                title : "Pay salary"
            }
        },
        {
            path: prefix + "payment-list",
            name: "admin.payment-list",
            component: PaymentList,
            meta : {
                title : "Payment list"
            }
        },
        {
            path: prefix + "staff-view",
            name: "admin.staff-view",
            component: StaffTree,
            meta: {
                title: "Staff Tree"
            }
        },
        {
            path: prefix + "site-settings",
            name: "admin.settings",
            component: Settings,
            meta: {
                title : "Site settings"
            }
        },
        {
            path: prefix + "create-exam",
            name: "admin.add-exam",
            component: AddExam,
            meta: {
                title: "Add exam"
            }
        },
        {
            path: prefix + "exam-list",
            name: "admin.exam-list",
            component: ExamList,
            meta: {
                title: "Exam List"
            }
        },
        {
            path: prefix+ "exam/:examId/questions",
            name: "admin.exam-questions",
            component: Questions,
            meta: {
                title : "Questions"
            }
        },
        {
            path: prefix + "edit-exam/:examId",
            name: "admin.edit-exam",
            component: EditExam,
            meta: {
                title: "Edit Exam"
            }
        },
    ],
    scrollBehavior(to, from, savedPos) {
        if (savedPos) {
            return savedPos;
        } else {
            return { x: 0, y: 0 };
        }
    }
});

routes.beforeEach((to, from, next) => {
    document.title = to.meta.title || "Dashboard";
    Vue.prototype.$Progress.start();
    next();
});

routes.afterEach(() => {
    Vue.prototype.$Progress.finish();
});

export default routes;