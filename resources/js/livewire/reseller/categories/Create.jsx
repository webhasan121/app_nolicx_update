import { useEffect, useMemo, useState } from "react";
import InputField from "../../../components/InputField";
import InputFile from "../../../components/InputFile";
import PrimaryButton from "../../../components/PrimaryButton";
import Hr from "../../../components/Hr";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";

export default function CreateCategory({ form, parentCategories = [] }) {
    const [previewUrl, setPreviewUrl] = useState(null);
    const parentCategoryItems = useMemo(
        () => flattenParentCategories(parentCategories),
        [parentCategories]
    );

    useEffect(() => {
        if (!(form.data.image instanceof File)) {
            setPreviewUrl(null);
            return undefined;
        }

        const url = URL.createObjectURL(form.data.image);
        setPreviewUrl(url);

        return () => URL.revokeObjectURL(url);
    }, [form.data.image]);

    return (
        <form onSubmit={form.submit}>
            <Section>
                <SectionHeader
                    title="Category"
                    content="Get a new category."
                />

                <SectionInner>
                    <InputField
                        name="name"
                        className="w-full"
                        labelWidth="250px"
                        error={form.errors.name}
                        label="Your Category Name"
                        value={form.data.name}
                        onChange={(e) => form.setData("name", e.target.value)}
                    />

                    <Hr />

                    <InputField
                        name="slug"
                        className="w-full"
                        labelWidth="250px"
                        error={form.errors.slug}
                        label="SEO Slug"
                        value={form.data.slug}
                        onChange={(e) => form.setData("slug", e.target.value)}
                    />

                    <Hr />

                    <div className="mb-4">
                        {previewUrl ? (
                            <img
                                src={previewUrl}
                                width="100"
                                height="100"
                                className="border rounded shadow"
                                alt=""
                            />
                        ) : null}

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
                                form.setData("image", e.target.files?.[0] ?? null)
                            }
                            className="hidden block w-full mt-1 border rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />

                        {form.errors.image ? (
                            <div className="text-sm text-red-500">
                                {form.errors.image}
                            </div>
                        ) : null}
                    </div>

                    <Hr />

                    <InputFile
                        label="Parent"
                        error="parent_id"
                        errors={form.errors}
                    >
                        <SearchableParentCategory
                            categories={parentCategoryItems}
                            value={form.data.parent_id}
                            onChange={(categoryId) =>
                                form.setData("parent_id", categoryId)
                            }
                        />
                    </InputFile>

                    <Hr />

                    <PrimaryButton>save</PrimaryButton>
                </SectionInner>
            </Section>
        </form>
    );
}

function SearchableParentCategory({ categories = [], value, onChange }) {
    const [isOpen, setIsOpen] = useState(false);
    const [search, setSearch] = useState("");
    const selectedCategory = categories.find(
        (category) => String(category.id) === String(value)
    );
    const normalizedSearch = search.trim().toLowerCase();
    const visibleValue = isOpen ? search : selectedCategory?.label ?? "";
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
        <div className="relative">
            <input
                type="text"
                value={visibleValue}
                onFocus={() => {
                    setSearch("");
                    setIsOpen(true);
                }}
                onChange={(event) => {
                    setSearch(event.target.value);
                    onChange("");
                    setIsOpen(true);
                }}
                onBlur={() => {
                    window.setTimeout(() => {
                        setSearch("");
                        setIsOpen(false);
                    }, 150);
                }}
                placeholder="Select Parent Category"
                className="w-full border-gray-300 rounded focus:border-blue-500 focus:ring-blue-500"
                autoComplete="off"
            />

            {isOpen ? (
                <div className="absolute left-0 right-0 z-30 mt-1 max-h-64 overflow-y-auto rounded border border-gray-200 bg-white shadow-lg">
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
                        Select Parent Category
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

function flattenParentCategories(categories = [], depth = 0) {
    return categories.flatMap((category) => {
        const prefix = depth === 0 ? "" : `${"-".repeat(depth * 2)} `;
        const current = {
            id: category.id,
            label: `${prefix}${category.name}`,
            searchText: String(category.name ?? "").toLowerCase(),
            disabled: depth >= 2,
        };

        return [
            current,
            ...flattenParentCategories(category.children ?? [], depth + 1),
        ];
    });
}
