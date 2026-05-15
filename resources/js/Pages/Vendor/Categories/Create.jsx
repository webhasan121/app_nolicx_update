import { Head, useForm } from "@inertiajs/react";
import AppLayout from "../../../Layouts/App";
import Hr from "../../../components/Hr";
import InputField from "../../../components/InputField";
import InputFile from "../../../components/InputFile";
import PrimaryButton from "../../../components/PrimaryButton";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import SectionSection from "../../../components/dashboard/section/Section";
import useTranslation from "../../../hooks/useTranslation";

export default function Create() {
    const { t } = useTranslation();
    const form = useForm({
        name: "",
        image: null,
    });

    const save = (e) => {
        e.preventDefault();
        form.post(route("vendor.category.store"), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    return (
        <AppLayout title={t("Category Create")} header={<PageHeader>{t("Category Create")}</PageHeader>}>
            <Head title={t("Category Create")} />

            <form onSubmit={save}>
                <Container>
                    <SectionSection>
                        <SectionHeader
                            title={t("Category")}
                            content={t("Get a new category.")}
                        />

                        <SectionInner>
                            <InputField
                                name="name"
                                className="md:flex"
                                labelWidth="250px"
                                label={t("Your Category Name")}
                                value={form.data.name}
                                onChange={(e) => form.setData("name", e.target.value)}
                                error={form.errors.name}
                            />
                            <Hr />
                            <InputFile label={t("Category Image")} error="image" errors={form.errors}>
                                <input
                                    type="file"
                                    onChange={(e) => form.setData("image", e.target.files?.[0] ?? null)}
                                />
                            </InputFile>
                            <PrimaryButton disabled={form.processing}>{t("save")}</PrimaryButton>
                        </SectionInner>
                    </SectionSection>
                </Container>
            </form>
        </AppLayout>
    );
}

