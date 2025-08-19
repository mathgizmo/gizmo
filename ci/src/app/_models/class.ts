export class ClassModel {
    id: number;
    key: string;
    name: string;
    class_type: string;
    subscription_type: string;
    invitations: string;
    teacher_id: number;
    teacher: string;
    is_researchable: number;
    // tags assigned to the class (from backend)
    tags?: Array<{ id: number; name: string }>;
}
