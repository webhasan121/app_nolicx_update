import { Head, router } from "@inertiajs/react";
import DangerButton from "@/Components/DangerButton";
import NavLink from "@/Components/NavLink";
import Container from "@/components/dashboard/Container";
import SectionSection from "@/components/dashboard/section/Section";
import UserDash from "@/components/user/dash/UserDash";

export default function Logout() {
    const submit = () => {
        router.post(route("logout.perform"));
    };

    return (
        <UserDash>
            <Head title="Logout" />

            <Container>
                <SectionSection>
                    <div className="alert alert-danger">
                        <div className="mb-3 text-md">
                            Are you sure to logout from your current session.
                        </div>

                        <div className="flex items-center gap-3">
                            <DangerButton onClick={submit}>Log Out</DangerButton>

                            <NavLink href={route("user.dash")}>Cancel</NavLink>
                        </div>
                    </div>
                </SectionSection>
            </Container>
        </UserDash>
    );
}
