import { Fragment, useEffect, useState } from "react";
import InputField from "../../../components/InputField";
import InputFile from "../../../components/InputFile";
import PrimaryButton from "../../../components/PrimaryButton";
import Hr from "../../../components/Hr";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";

export default function CreateCategory({ form, parentCategories = [] }) {
    const [previewUrl, setPreviewUrl] = useState(null);

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
                        <select
                            value={form.data.parent_id}
                            onChange={(e) =>
                                form.setData("parent_id", e.target.value)
                            }
                            className="border-gray-300 rounded focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Select Parent Category</option>
                            {renderCategoryOptions(parentCategories)}
                        </select>
                    </InputFile>

                    <Hr />

                    <PrimaryButton>save</PrimaryButton>
                </SectionInner>
            </Section>
        </form>
    );
}

function renderCategoryOptions(categories = [], depth = 0) {
    return categories.map((category) => (
        <Fragment key={category.id}>
            <option value={category.id} disabled={depth >= 2}>
                {`${depth === 0 ? "" : "-".repeat(depth * 2) + " "}${category.name}`}
            </option>
            {renderCategoryOptions(category.children ?? [], depth + 1)}
        </Fragment>
    ));
}
