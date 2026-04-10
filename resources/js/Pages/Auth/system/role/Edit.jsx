import { Head, useForm } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import DangerButton from "../../../../components/DangerButton";
import Hr from "../../../../components/Hr";
import PrimaryButton from "../../../../components/PrimaryButton";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import PermissionsToUser from "../../../../components/PermissionsToUser";

export default function Edit({ role, permissions = [], userPermissions = [] }) {
    const permissionForm = useForm({
        permissions: userPermissions ?? [],
    });

    const submitPermissions = (e) => {
        e.preventDefault();
        permissionForm.post(route("system.permissions.to-role", { role: role.id }));
    };

    return (
        <AppLayout
            title="Role Edit"
            header={<PageHeader>Role Edit</PageHeader>}
        >
            <Head title="Role Edit" />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <strong className="text-lg">
                                {(role?.name ?? "Not Found").toUpperCase()}
                            </strong>
                        }
                        content={
                            <>
                                Edit your {role?.name} role. add or remove permission from all ({role?.permissions_count ?? 0}) Permissiions.
                                <Hr />
                                <div className="space-x-2 space-y-2" />
                            </>
                        }
                    />
                </Section>
            </Container>

            <Container>
                <Section>
                    <SectionHeader
                        title={`Permissions (${role?.permissions_count ?? 0})`}
                        content={
                            <p>
                                Add or Remove permission from all ({permissions.length}) Permissiions.
                            </p>
                        }
                    />

                    <SectionInner>
                        <form onSubmit={submitPermissions}>
                            <PermissionsToUser
                                permissions={permissions}
                                userPermissions={userPermissions}
                                selectedPermissions={permissionForm.data.permissions}
                                onChange={(next) => permissionForm.setData("permissions", next)}
                            />
                            <Hr />
                            {role?.name !== "system" ? (
                                <PrimaryButton type="submit" className="mt-4 border-0">
                                    Update
                                </PrimaryButton>
                            ) : (
                                <DangerButton type="submit" className="border-0 text-danger">
                                    System Permission can't be omitted.
                                </DangerButton>
                            )}
                        </form>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
