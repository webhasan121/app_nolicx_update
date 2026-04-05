import { Head, router, useForm } from "@inertiajs/react";
import { useEffect, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Modal from "../../../../components/Modal";
import PrimaryButton from "../../../../components/PrimaryButton";
import Container from "../../../../components/dashboard/Container";
import Chr from "../../../../components/dashboard/Chr";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import CreateCategory from "../../../../livewire/reseller/categories/Create";

export default function Index({
    categories = [],
    categoryCount = 0,
    parentCategories = [],
}) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const form = useForm({
        name: "",
        slug: "",
        image: null,
        parent_id: "",
    });

    useEffect(() => {
        if (form.data.name && !form.isDirty) {
            return;
        }

        form.setData("slug", slugify(form.data.name));
    }, [form.data.name]);

    const handleSubmit = (e) => {
        e.preventDefault();
        form.post(route("system.categories.store"), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                setShowCreateModal(false);
                form.reset();
            },
        });
    };

    const createForm = {
        data: form.data,
        errors: form.errors,
        setData: form.setData,
        submit: handleSubmit,
    };

    const handleDelete = (id) => {
        if (!window.confirm("Are you sure you want to delete this category?")) {
            return;
        }

        router.delete(route("system.categories.destroy", { category: id }), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout title="Categories">
            <Head title="Categories" />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex justify-between items-center">
                                <div>
                                    Categories{" "}
                                    <span className="text-sm text-gray-500">
                                        ({categoryCount})
                                    </span>
                                </div>
                                <PrimaryButton
                                    className="ml-2"
                                    type="button"
                                    onClick={() => setShowCreateModal(true)}
                                >
                                    <i className="fas fa-plus pr-2"></i>{" "}
                                    Category
                                </PrimaryButton>
                            </div>
                        }
                        content="Manage your categories and subcategories here."
                    />

                    <SectionInner>
                        {categories.map((item, index) => (
                            <Chr
                                key={item.id}
                                item={item}
                                loop={index + 1}
                                collapse={false}
                                onDelete={handleDelete}
                            />
                        ))}
                    </SectionInner>
                </Section>
            </Container>

            <Modal show={showCreateModal} onClose={() => setShowCreateModal(false)}>
                <div className="p-6">
                    <CreateCategory
                        form={createForm}
                        parentCategories={parentCategories}
                    />
                </div>
            </Modal>
        </AppLayout>
    );
}

function slugify(value) {
    return (value ?? "")
        .toString()
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, "")
        .replace(/\s+/g, "-")
        .replace(/-+/g, "-");
}
