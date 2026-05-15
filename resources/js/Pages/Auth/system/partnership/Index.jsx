import AppLayout from "../../../../Layouts/App";
import PageHeader from "../../../../components/dashboard/PageHeader";
import useTranslation from "../../../../hooks/useTranslation";

export default function Index() {
    const { t } = useTranslation();
    return (
        <AppLayout
            title={t("Partnership")}
            header={<PageHeader>{t("Partnership")}</PageHeader>}
        >
            <div>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsum recusandae ullam doloribus, debitis quos molestiae sed, dignissimos, quae reiciendis tempora pariatur temporibus vitae! Enim adipisci corporis modi optio ipsam molestiae!
            </div>
        </AppLayout>
    );
}
