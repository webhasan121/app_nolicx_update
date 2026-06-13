import { Head, useForm } from "@inertiajs/react";
import { useEffect, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Hr from "../../../../components/Hr";
import CategorySelect from "../../../../components/CategorySelect";
import InputLabel from "../../../../components/InputLabel";
import Modal from "../../../../components/Modal";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import CreateCategory from "../../../../livewire/reseller/categories/Create";
import useTranslation from "../../../../hooks/useTranslation";

export default function Edit({ category, parentCategories = [] }) {
    const { t } = useTranslation();
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [previewUrl, setPreviewUrl] = useState(null);

    const form = useForm({
        name: category?.name ?? "",
        slug: category?.slug ?? "",
        belongs_to: category?.belongs_to ?? "",
        newImage: null,
    });

    const createForm = useForm({
        name: "",
        slug: "",
        image: null,
        parent_id: "",
    });

    useEffect(() => {
        if (!(form.data.newImage instanceof File)) {
            setPreviewUrl(null);
            return undefined;
        }

        const url = URL.createObjectURL(form.data.newImage);
        setPreviewUrl(url);

        return () => URL.revokeObjectURL(url);
    }, [form.data.newImage]);

    const submitEdit = (e) => {
        e.preventDefault();
        form.post(route("system.categories.update", { cid: category.id }), {
            forceFormData: true,
        });
    };

    const submitCreate = (e) => {
        e.preventDefault();
        createForm.post(route("system.categories.store"), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                setShowCreateModal(false);
                createForm.reset();
            },
        });
    };

    return (
        <AppLayout title={t("Edit Category")}>
            <Head title={t("Edit Category")} />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-start justify-between w-full">{t("Edit Category")}<PrimaryButton
                                    className="ml-2"
                                    type="button"
                                    onClick={() => setShowCreateModal(true)}
                                >
                                    <i className="pr-2 fas fa-plus"></i>{t("Category")}</PrimaryButton>
                            </div>
                        }
                        content={t("Modify the details of the selected category.")}
                    />

                    <SectionInner>
                        <form
                            onSubmit={submitEdit}
                            className="p-3 space-y-4 border rounded-md"
                            style={{ maxWidth: 350, margin: "auto" }}
                        >
                            <div className="mb-4">
                                <InputLabel
                                    htmlFor="parent_id"
                                    className="block text-sm font-medium text-gray-700"
                                >
                                    Parent
                                </InputLabel>
                                <CategorySelect
                                    categories={parentCategories}
                                    value={form.data.belongs_to}
                                    onChange={(categoryId) =>
                                        form.setData("belongs_to", categoryId)
                                    }
                                    placeholder="Search parent category"
                                    noneLabel="None"
                                    currentCategoryId={category?.id}
                                />
                                {form.errors.belongs_to ? (
                                    <span className="text-sm text-red-500">
                                        {form.errors.belongs_to}
                                    </span>
                                ) : null}
                            </div>

                            <Hr />

                            <div className="mb-4">
                                <InputLabel
                                    htmlFor="name"
                                    className="block text-sm font-medium text-gray-700"
                                >
                                    Category Name
                                </InputLabel>
                                <TextInput
                                    type="text"
                                    id="name"
                                    value={form.data.name}
                                    onChange={(e) =>
                                        form.setData("name", e.target.value)
                                    }
                                    className="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required
                                />
                                {form.errors.name ? (
                                    <span className="text-sm text-red-500">
                                        {form.errors.name}
                                    </span>
                                ) : null}
                            </div>
                            <div className="mb-4">
                                <InputLabel
                                    htmlFor="slug"
                                    className="block text-sm font-medium text-gray-700"
                                >
                                    SEO Slug
                                </InputLabel>
                                <TextInput
                                    type="text"
                                    id="slug"
                                    value={form.data.slug}
                                    disabled
                                    readOnly
                                    className="block w-full mt-1 text-gray-500 bg-gray-100 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required
                                />
                                {form.errors.slug ? (
                                    <span className="text-sm text-red-500">
                                        {form.errors.slug}
                                    </span>
                                ) : null}
                            </div>
                            <div className="mb-4">
                                {previewUrl ? (
                                    <img
                                        src={previewUrl}
                                        width="100"
                                        height="100"
                                        className="border rounded shadow"
                                        alt=""
                                    />
                                ) : (
                                    <img
                                        src={`/storage/${category.image}`}
                                        width="100"
                                        height="100"
                                        className="border rounded shadow"
                                        alt=""
                                    />
                                )}

                                <label
                                    htmlFor="image"
                                    className="inline-block p-2 text-sm font-medium text-gray-700 border rounded text-end"
                                >
                                    <i className="pr-2 fas fa-upload"></i>{t("Upload")}</label>
                                <input
                                    type="file"
                                    id="image"
                                    onChange={(e) =>
                                        form.setData(
                                            "newImage",
                                            e.target.files?.[0] ?? null
                                        )
                                    }
                                    className="hidden block w-full mt-1 border rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                />

                                {form.errors.newImage ? (
                                    <span className="text-sm text-red-500">
                                        {form.errors.newImage}
                                    </span>
                                ) : null}
                            </div>

                            <Hr />

                            <div className="flex justify-end">
                                <PrimaryButton type="submit">
                                    <i className="pr-2 fas fa-save"></i>{t("Save Changes")}</PrimaryButton>
                            </div>
                        </form>
                    </SectionInner>
                </Section>
            </Container>

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)}>
                <div className="p-6">
                    <CreateCategory
                        form={{
                            data: createForm.data,
                            errors: createForm.errors,
                            setData: createForm.setData,
                            submit: submitCreate,
                        }}
                        parentCategories={parentCategories}
                    />
                </div>
            </Modal>
        </AppLayout>
    );
}
