import { Head, useForm } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Hr from "../../../../components/Hr";
import InputLabel from "../../../../components/InputLabel";
import Modal from "../../../../components/Modal";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import Container from "../../../../components/dashboard/Container";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import CreateCategory from "../../../../livewire/reseller/categories/Create";

export default function Edit({ category, parentCategories = [] }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [previewUrl, setPreviewUrl] = useState(null);
    const parentCategoryItems = useMemo(
        () => flattenParentCategories(parentCategories, category?.id),
        [parentCategories, category?.id]
    );

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
        <AppLayout title="Edit Category">
            <Head title="Edit Category" />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex justify-between items-start w-full">
                                Edit Category
                                <PrimaryButton
                                    className="ml-2"
                                    type="button"
                                    onClick={() => setShowCreateModal(true)}
                                >
                                    <i className="fas fa-plus pr-2"></i> Category
                                </PrimaryButton>
                            </div>
                        }
                        content="Modify the details of the selected category."
                    />

                    <SectionInner>
                        <form
                            onSubmit={submitEdit}
                            className="space-y-4 p-3 border rounded-md"
                            style={{ maxWidth: 350, margin: "auto" }}
                        >
                            <div className="mb-4">
                                <InputLabel
                                    htmlFor="parent_id"
                                    className="block text-sm font-medium text-gray-700"
                                >
                                    Parent
                                </InputLabel>
                                <SearchableParentCategory
                                    categories={parentCategoryItems}
                                    value={form.data.belongs_to}
                                    onChange={(categoryId) =>
                                        form.setData("belongs_to", categoryId)
                                    }
                                />
                                {form.errors.belongs_to ? (
                                    <span className="text-red-500 text-sm">
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
                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required
                                />
                                {form.errors.name ? (
                                    <span className="text-red-500 text-sm">
                                        {form.errors.name}
                                    </span>
                                ) : null}
                            </div>
                            <div className="mb-4">
                                <InputLabel
                                    htmlFor="slug"
                                    className="block text-sm font-medium text-gray-700"
                                >
                                    Category Name
                                </InputLabel>
                                <TextInput
                                    type="text"
                                    id="slug"
                                    value={form.data.slug}
                                    onChange={(e) =>
                                        form.setData("slug", e.target.value)
                                    }
                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required
                                />
                                {form.errors.slug ? (
                                    <span className="text-red-500 text-sm">
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
                                    <i className="pr-2 fas fa-upload"></i> Upload
                                </label>
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
                                    <span className="text-red-500 text-sm">
                                        {form.errors.newImage}
                                    </span>
                                ) : null}
                            </div>

                            <Hr />

                            <div className="flex justify-end">
                                <PrimaryButton type="submit">
                                    <i className="fas fa-save pr-2"></i>Save Changes
                                </PrimaryButton>
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

function SearchableParentCategory({ categories = [], value, onChange }) {
    const [isOpen, setIsOpen] = useState(false);
    const [search, setSearch] = useState("");
    const selectedCategory = categories.find(
        (category) => String(category.id) === String(value)
    );
    const normalizedSearch = search.trim().toLowerCase();
    const visibleValue = isOpen ? search : selectedCategory?.label ?? "None";
    const filteredCategories = categories.filter((category) =>
        category.searchText.includes(normalizedSearch)
    );

    const selectCategory = (category) => {
        if (category.disabled) {
            return;
        }

        onChange(category.id);
        setSearch("");
        setIsOpen(false);
    };

    return (
        <div className="relative mt-1">
            <input
                type="text"
                id="parent_id"
                value={visibleValue}
                onFocus={() => {
                    setSearch("");
                    setIsOpen(true);
                }}
                onChange={(event) => {
                    setSearch(event.target.value);
                    setIsOpen(true);
                }}
                onBlur={() => {
                    window.setTimeout(() => {
                        setSearch("");
                        setIsOpen(false);
                    }, 150);
                }}
                placeholder="Search parent category"
                className="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                autoComplete="off"
            />
            <i className="absolute text-gray-500 -translate-y-1/2 pointer-events-none fas fa-angle-down right-3 top-1/2"></i>

            {isOpen ? (
                <div className="absolute left-0 right-0 z-30 mt-1 max-h-64 overflow-y-auto rounded-md border border-gray-200 bg-white shadow-lg">
                    <button
                        type="button"
                        className="block w-full px-3 py-2 text-left text-sm hover:bg-gray-100 focus:bg-gray-100"
                        onMouseDown={(event) => event.preventDefault()}
                        onClick={() => {
                            onChange("");
                            setSearch("");
                            setIsOpen(false);
                        }}
                    >
                        None
                    </button>

                    {filteredCategories.length > 0 ? (
                        filteredCategories.map((category) => (
                            <button
                                key={category.id}
                                type="button"
                                disabled={category.disabled}
                                className={`block w-full px-3 py-2 text-left text-sm ${
                                    category.disabled
                                        ? "cursor-not-allowed text-gray-400"
                                        : "hover:bg-gray-100 focus:bg-gray-100"
                                }`}
                                onMouseDown={(event) => event.preventDefault()}
                                onClick={() => selectCategory(category)}
                            >
                                {category.label}
                            </button>
                        ))
                    ) : (
                        <div className="px-3 py-4 text-center text-sm text-gray-500">
                            No category found.
                        </div>
                    )}
                </div>
            ) : null}
        </div>
    );
}

function flattenParentCategories(categories = [], currentCategoryId = null, depth = 0, blocked = false) {
    return categories.flatMap((item) => {
        const isCurrentCategory = String(item.id) === String(currentCategoryId);
        const isBlocked = blocked || isCurrentCategory;
        const prefix = depth === 0 ? "" : `${"-".repeat(depth * 2)} `;
        const current = {
            id: item.id,
            label: `${prefix}${item.name}`,
            searchText: `${item.name ?? ""} ${prefix}${item.name ?? ""}`.toLowerCase(),
            disabled: isBlocked,
        };

        return [
            current,
            ...flattenParentCategories(
                item.children ?? [],
                currentCategoryId,
                depth + 1,
                isBlocked
            ),
        ];
    });
}
