import { usePage } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import PrimaryButton from "../../../../components/PrimaryButton";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import Table from "../../../../components/dashboard/table/Table";
import NavLink from "../../../../components/NavLink";
import useTranslation from "../../../../hooks/useTranslation";

export default function Index() {
    const { t } = useTranslation();
    const { admins = [] } = usePage().props;

    return (
        <AppLayout title={t("Admins")} header={<PageHeader>{t("Admins")}</PageHeader>}>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={t("Your system admins")}
                        content={`You have ${admins.length ?? "N/A"} admin with different permissions`}
                    />

                    <SectionInner>
                        <Table data={admins}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{t("Name")}</th>
                                    <th>{t("Permissions")}</th>
                                    <th>{t("Assign At")}</th>
                                    <th>{t("Action")}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {admins.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td>{item.name ?? "N/A"}</td>
                                        <td>
                                            {item.permissions_count ?? "N/A"}
                                        </td>
                                        <td>
                                            {item.updated_at_formatted ?? "N/A"}
                                        </td>
                                        <td>
                                            <NavLink
                                                href={route(
                                                    "system.users.edit",
                                                    {
                                                        id: item.id,
                                                    },
                                                )}
                                            >
                                                <PrimaryButton type="button">{t("Edit")}</PrimaryButton>
                                            </NavLink>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </SectionSection>
            </Container>
        </AppLayout>
    );
}
