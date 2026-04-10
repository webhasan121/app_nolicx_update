import { Head, router, useForm } from "@inertiajs/react";
import AppLayout from "../../../Layouts/App";
import DangerButton from "../../../components/DangerButton";
import Hr from "../../../components/Hr";
import InputField from "../../../components/InputField";
import InputFile from "../../../components/InputFile";
import NavLink from "../../../components/NavLink";
import PrimaryButton from "../../../components/PrimaryButton";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import SectionSection from "../../../components/dashboard/section/Section";
import Table from "../../../components/dashboard/table/Table";

export default function Index({ categories = [] }) {
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
        router.delete(route("vendor.category.destroy", { category: id }), {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout title="Categories" header={<PageHeader>Categories</PageHeader>}>
            <Head title="Categories" />

            <form onSubmit={save}>
                <Container>
                    <SectionSection>
                        <SectionHeader
                            title="Category"
                            content="Get a new category."
                        />
                        <SectionInner>
                            <InputField
                                name="name"
                                className="md:flex"
                                labelWidth="250px"
                                label="Your Category Name"
                                value={form.data.name}
                                onChange={(e) => form.setData("name", e.target.value)}
                                error={form.errors.name}
                            />
                            <Hr />
                            <InputFile label="Category Image" error="image" errors={form.errors}>
                                <input
                                    type="file"
                                    onChange={(e) => form.setData("image", e.target.files?.[0] ?? null)}
                                />
                            </InputFile>
                            <PrimaryButton disabled={form.processing}>save</PrimaryButton>
                        </SectionInner>
                    </SectionSection>
                </Container>
            </form>

            <Container>
                <SectionSection>
                    <SectionHeader
                        title="Categories List"
                        content="View and Edit your listed categories"
                    />
                    <SectionInner>
                        <Table data={categories}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Owner</th>
                                    <th>Product</th>
                                    <th>Created</th>
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
                                            <NavLink href={route("vendor.category.edit", { cat: item.id })}>
                                                <PrimaryButton type="button">edit</PrimaryButton>
                                            </NavLink>
                                            <DangerButton
                                                type="button"
                                                onClick={() => remove(item.id)}
                                            >
                                                delete
                                            </DangerButton>
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

