import { Head, useForm } from "@inertiajs/react";
import AppLayout from "../../../../../Layouts/App";
import InputLabel from "../../../../../components/InputLabel";
import NavLink from "../../../../../components/NavLink";
import PrimaryButton from "../../../../../components/PrimaryButton";
import SecondaryButton from "../../../../../components/SecondaryButton";
import TextInput from "../../../../../components/TextInput";
import Container from "../../../../../components/dashboard/Container";
import PageHeader from "../../../../../components/dashboard/PageHeader";
import Section from "../../../../../components/dashboard/section/Section";
import NavLinkBtn from "../../../../../components/NavLinkBtn";

function slugify(value) {
    return String(value || "")
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, "")
        .replace(/\s+/g, "-")
        .replace(/-+/g, "-");
}

export default function Create() {
    const form = useForm({
        name: "",
        email: "",
        phone: "",
        slug: "",
        address: "",
    });

    const submit = (e) => {
        e.preventDefault();
        form.post(route("system.branches.store"));
    };

    const updateName = (e) => {
        const value = e.target.value;
        form.setData("name", value);
        form.setData("slug", slugify(value));
    };

    return (
        <AppLayout
            title="Create A New Branch"
            header={
                <PageHeader>
                    Create A New Branch
                    <br />
                    <NavLink href={route("system.branches.index")} className="">
                        <i className="pr-2 fas fa-angle-left"></i>
                        <span>Back</span>
                    </NavLink>
                </PageHeader>
            }
        >
            <Head title="Create A New Branch" />

            <Container>
                <form onSubmit={submit} className="space-y-6">
                    <div className="flex flex-col gap-8 lg:flex-row">
                        <div className="w-lg">
                            <Section className="w-full">
                                <div className="grid grid-cols-1 gap-5">
                                    <div className="relative">
                                        <InputLabel
                                            htmlFor="name"
                                            value="Branch Name"
                                        />
                                        <TextInput
                                            id="name"
                                            type="text"
                                            className="w-full"
                                            placeholder="Test Branch"
                                            value={form.data.name}
                                            onChange={updateName}
                                        />
                                        {form.errors.name && (
                                            <div className="text-sm text-red-600">
                                                {form.errors.name}
                                            </div>
                                        )}
                                    </div>

                                    <div className="relative">
                                        <InputLabel
                                            htmlFor="slug"
                                            value="Slug"
                                        />
                                        <TextInput
                                            id="slug"
                                            type="text"
                                            className="w-full"
                                            placeholder="test-branch"
                                            value={form.data.slug}
                                            onChange={(e) =>
                                                form.setData(
                                                    "slug",
                                                    e.target.value,
                                                )
                                            }
                                        />
                                        {form.errors.slug && (
                                            <div className="text-sm text-red-600">
                                                {form.errors.slug}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </Section>
                        </div>
                        <div className="w-full">
                            <Section className="w-full">
                                <div className="space-y-5">
                                    <div className="grid gap-5 lg:grid-cols-2">
                                        <div className="relative">
                                            <InputLabel
                                                htmlFor="email"
                                                value="Email"
                                            />
                                            <TextInput
                                                id="email"
                                                type="email"
                                                className="w-full"
                                                placeholder="branch@example.com"
                                                value={form.data.email}
                                                onChange={(e) =>
                                                    form.setData(
                                                        "email",
                                                        e.target.value,
                                                    )
                                                }
                                            />
                                            {form.errors.email && (
                                                <div className="text-sm text-red-600">
                                                    {form.errors.email}
                                                </div>
                                            )}
                                        </div>

                                        <div className="relative">
                                            <InputLabel
                                                htmlFor="phone"
                                                value="Phone"
                                            />
                                            <TextInput
                                                id="phone"
                                                type="text"
                                                className="w-full"
                                                placeholder="+8801XXXXXXXXX"
                                                value={form.data.phone}
                                                onChange={(e) =>
                                                    form.setData(
                                                        "phone",
                                                        e.target.value,
                                                    )
                                                }
                                            />
                                            {form.errors.phone && (
                                                <div className="text-sm text-red-600">
                                                    {form.errors.phone}
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    <div className="relative">
                                        <InputLabel
                                            htmlFor="address"
                                            value="Address"
                                        />
                                        <TextInput
                                            id="address"
                                            type="text"
                                            className="w-full"
                                            placeholder="123 Main Street, Dhaka"
                                            value={form.data.address}
                                            onChange={(e) =>
                                                form.setData(
                                                    "address",
                                                    e.target.value,
                                                )
                                            }
                                        />
                                        {form.errors.address && (
                                            <div className="text-sm text-red-600">
                                                {form.errors.address}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </Section>
                        </div>
                    </div>

                    <div className="flex items-center justify-start gap-4 pt-4 border-t">
                        {/* <SecondaryButton
                            type="button"
                            onClick={() => window.location.assign(route("system.branches.index"))}
                        >
                            <span>Cancel</span>
                        </SecondaryButton> */}

                        <NavLinkBtn href={route("system.branches.index")}>
                            <span>Cancel</span>
                        </NavLinkBtn>

                        <PrimaryButton type="submit" disabled={form.processing}>
                            <span>
                                {form.processing
                                    ? "Saving..."
                                    : "Create Branch"}
                            </span>
                        </PrimaryButton>
                    </div>
                </form>
            </Container>
        </AppLayout>
    );
}
