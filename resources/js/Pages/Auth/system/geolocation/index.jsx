import { Head } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import useTranslation from "../../../../hooks/useTranslation";

export default function Index() {
    const { t } = useTranslation();
    return (
        <AppLayout
            title={t("Grolocations")}
            header={<PageHeader>{t("Grolocations")}</PageHeader>}
        >
            <Head title={t("Grolocations")} />

            <Container>
                <div className="flex items-center gap-2">
                    <NavLinkBtn href={route("system.geolocations.countries")}>Countries</NavLinkBtn>
                    <NavLinkBtn href={route("system.geolocations.states")}>States</NavLinkBtn>
                    <NavLinkBtn href={route("system.geolocations.cities")}>Cities</NavLinkBtn>
                    <NavLinkBtn href={route("system.geolocations.area")}>Areas</NavLinkBtn>
                </div>
            </Container>

            <Container>
                <Section>
                    <SectionHeader
                        title={t("Targeted Area")}
                        content={t("Manage your targeted area from here. You can add, edit and delete countries, states and cities.")}
                    />
                    <SectionInner></SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
