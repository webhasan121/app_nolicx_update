import { Head, router, useForm } from "@inertiajs/react";
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
import Table from "../../../components/dashboard/table/Table";
import useTranslation from "../../../hooks/useTranslation";
import { ActionIconButton, ActionIconLink } from "../../../components/ActionIcon";

export default function Index({ categories = [] }) {
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

    const remove = (id) => {
        if (!window.confirm("Are you sure you want to delete this category?")) {
            return;
        }

        router.delete(route("vendor.category.destroy", { category: id }), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout title={t("Categories")} header={<PageHeader>{t("Categories")}</PageHeader>}>
            <Head title={t("Categories")} />

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

            <Container>
                <SectionSection>
                    <SectionHeader
                        title={t("Categories List")}
                        content={t("View and Edit your listed categories")}
                    />
                    <SectionInner>
                        <Table data={categories}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{t("Name")}</th>
                                    <th>{t("Owner")}</th>
                                    <th>{t("Product")}</th>
                                    <th>{t("Created")}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {categories.map((item, index) => (
                                    <tr key={item.id}>
                                        <td>{index + 1}</td>
                                        <td>
                                            <div className="flex items-center">
                                                {item.image_url ? (
                                                    <img
                                                        width="40"
                                                        height="40"
                                                        src={item.image_url}
                                                        alt=""
                                                    />
                                                ) : null}
                                                {item.name ?? "N/A"}
                                            </div>
                                        </td>
                                        <td>{item.owner}</td>
                                        <td>{item.products_count}</td>
                                        <td>
                                            {item.created_at_human}
                                            <br />
                                            <span className="text-xs">{item.created_at_formatted}</span>
                                        </td>
                                        <td>
                                            <div className="flex items-center gap-1">
                                                <ActionIconLink href={route("vendor.category.edit", { cat: item.id })} action="edit" title={t("edit")} />
                                                <ActionIconButton
                                                    action="delete"
                                                    title={t("delete")}
                                                onClick={() => remove(item.id)}
                                                />
                                            </div>
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

