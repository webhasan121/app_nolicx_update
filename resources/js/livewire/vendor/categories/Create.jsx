import { useForm } from "@inertiajs/react";
import Hr from "../../../components/Hr";
import InputField from "../../../components/InputField";
import InputFile from "../../../components/InputFile";
import PrimaryButton from "../../../components/PrimaryButton";
import Container from "../../../components/dashboard/Container";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";

export default function Create({ action }) {
    const form = useForm({
        name: "",
        image: null,
    });

    const save = (e) => {
        e.preventDefault();
        form.post(action, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    return (
        <form onSubmit={save}>
            <Container>
                <Section>
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
                        <PrimaryButton disabled={form.processing}>
                            save
                        </PrimaryButton>
                    </SectionInner>
                </Section>
            </Container>
        </form>
    );
}
