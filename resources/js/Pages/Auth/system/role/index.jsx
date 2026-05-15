import { Head } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import NavLink from "../../../../components/NavLink";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Table from "../../../../components/dashboard/table/Table";
import useTranslation from "../../../../hooks/useTranslation";

export default function Index({ roles = [] }) {
    const { t } = useTranslation();
    return (
        <AppLayout
            title={t("Roles")}
            header={<PageHeader>{t("Roles")}</PageHeader>}
        >
            <Head title={t("Roles")} />

            <Container>
                <Section>
                    <SectionHeader
                        title={t("Role List")}
                        content={`system have all ${roles.length} role.`}
                    />

                    <SectionInner>
                        <Table data={roles}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{t("Role")}</th>
                                    <th>{t("Users")}</th>
                                    <th>{t("Permissions")}</th>
                                    <th>{t("Action")}</th>
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
                                                <NavLink href={route("system.role.edit", { role: role.encrypted_id })}>{t("Edit")}</NavLink>
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
