import { Head } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import NavLink from "../../../../components/NavLink";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";

export default function Index({ roles = [] }) {
    return (
        <AppLayout
            title="Roles"
            header={<PageHeader>Roles</PageHeader>}
        >
            <Head title="Roles" />

            <Container>
                <Section>
                    <SectionHeader
                        title="Role List"
                        content={`system have all ${roles.length} role.`}
                    />

                    <SectionInner>
                        <Table data={roles}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Role</th>
                                    <th>Users</th>
                                    <th>Permissions</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {roles.map((role, index) => (
                                    <tr key={role.id}>
                                        <td>{index + 1}</td>
                                        <td>{role.name ?? ""}</td>
                                        <td>{role.users_count ?? "No Users"}</td>
                                        <td>{role.permissions_count ?? "No Permissions"}</td>
                                        <td>
                                            <div className="flex">
                                                <NavLink href={route("system.role.edit", { role: role.encrypted_id })}>
                                                    Edit
                                                </NavLink>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
