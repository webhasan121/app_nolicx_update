import { Head, useForm } from "@inertiajs/react";
import AppLayout from "../../../Layouts/App";
import Hr from "../../../components/Hr";
import InputField from "../../../components/InputField";
import InputFile from "../../../components/InputFile";
import PrimaryButton from "../../../components/PrimaryButton";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import SectionInner from "../../../components/dashboard/section/Inner";
import SectionSection from "../../../components/dashboard/section/Section";

export default function Edit({ category }) {
    const form = useForm({
        name: category?.name ?? "",
        image: null,
    });

    const preview = form.data.image ? URL.createObjectURL(form.data.image) : null;

    const save = (e) => {
        e.preventDefault();
        form.post(route("vendor.category.update", { cat: category?.id }), {
            forceFormData: true,
            preserveScroll: true,
        });
    };

    return (
        <AppLayout title="Category Update" header={<PageHeader>Category Update</PageHeader>}>
            <Head title="Category Update" />

            <form onSubmit={save}>
                <Container>
                    <SectionSection>
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
                                {!preview && category?.image_url ? (
                                    <img width="100" height="100" src={category.image_url} alt="" />
                                ) : null}
                                {preview ? (
                                    <img width="100" height="100" src={preview} alt="" />
                                ) : null}
                                <input
                                    type="file"
                                    onChange={(e) => form.setData("image", e.target.files?.[0] ?? null)}
                                />
                            </InputFile>
                            <PrimaryButton disabled={form.processing}>update</PrimaryButton>
                        </SectionInner>
                    </SectionSection>
                </Container>
            </form>
        </AppLayout>
    );
}

