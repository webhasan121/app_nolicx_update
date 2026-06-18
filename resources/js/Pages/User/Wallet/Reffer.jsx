import { usePage } from "@inertiajs/react";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";
import UserDash from "../../../components/user/dash/UserDash";
import { formatCurrency } from "../../../utils/formatAmount";
import useTranslation from "../../../hooks/useTranslation";

export default function Reffer() {
    const { t } = useTranslation();
    const { refs = [] } = usePage().props;

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={t("VIP Ref Comission")}
                        content={t("If your ref user purchase a vip package, then you will get the comissions.")}
                    />

                    <SectionInner>
                        <Table data={refs}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{t("Comission")}</th>
                                    <th>{t("User")}</th>
                                    <th>{t("Date")}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {refs.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td>{formatCurrency(item.comission)}</td>
                                        <td>{item.user}</td>
                                        <td>{item.date}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </SectionSection>
            </Container>
        </UserDash>
    );
}
