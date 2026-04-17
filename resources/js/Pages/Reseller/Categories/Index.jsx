import { Head } from "@inertiajs/react";
import AppLayout from "../../../Layouts/App";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import Table from "../../../components/dashboard/table/Table";
import CreateCategory from "../../../livewire/vendor/categories/Create";

export default function Index({ categories = [] }) {
    return (
        <AppLayout title="Categories" header={<PageHeader>Categories</PageHeader>}>
            <Head title="Categories" />

            <CreateCategory action={route("reseller.categories.store")} />

            <Container>
                <Section>
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
                                </tr>
                            </thead>
                            <tbody>
                                {categories.map((item) => (
                                    <tr key={item.id}>
                                        <td>{item.sl}</td>
                                        <td>{item.name ?? "N/A"}</td>
                                        <td>{item.owner ?? "N/A"}</td>
                                        <td>{item.products_count ?? "0"}</td>
                                        <td>
                                            {item.created_at_human ?? "N/A"}
                                            <br />
                                            <span className="text-xs">
                                                {item.created_at_formatted ?? "N/A"}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </Section>
            </Container>
        </AppLayout>
    );
}
