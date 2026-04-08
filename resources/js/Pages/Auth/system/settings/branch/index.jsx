import { Head, router } from "@inertiajs/react";
import AppLayout from "../../../../../Layouts/App";
import DangerButton from "../../../../../components/DangerButton";
import NavLinkBtn from "../../../../../components/NavLinkBtn";
import Container from "../../../../../components/dashboard/Container";
import PageHeader from "../../../../../components/dashboard/PageHeader";
import Section from "../../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../../components/dashboard/section/Header";
import SectionInner from "../../../../../components/dashboard/section/Inner";
import Table from "../../../../../components/dashboard/table/Table";

export default function Index({ branches = [] }) {
    const destroy = (id) => {
        if (!window.confirm("Are you sure you want to delete this branch?")) {
            return;
        }

        router.delete(route("system.branches.destroy", { id }));
    };

    return (
        <AppLayout
            title="Settings"
            header={<PageHeader>Settings</PageHeader>}
        >
            <Head title="Settings" />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="flex items-center justify-between">
                                <div>Branch Management</div>
                                <NavLinkBtn href={route("system.branches.create")}>
                                    <i className="fas fa-plus pr-2"></i>
                                    <span>Branch</span>
                                </NavLinkBtn>
                            </div>
                        }
                        content="Setup your necessary branches from here. add, edit and delete."
                    />

                    <SectionInner>
                        <Table data={branches}>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Created</th>
                                    <th width="60">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {branches.map((branch) => (
                                    <tr key={branch.id}>
                                        <td>{branch.sl}</td>
                                        <td>{branch.name}</td>
                                        <td>{branch.email}</td>
                                        <td>{branch.type}</td>
                                        <td>{branch.created_at}</td>
                                        <td>
                                            <div className="flex items-center gap-2">
                                                <NavLinkBtn href={route("system.branches.modify", branch.id)}>
                                                    <i className="fas fa-edit"></i>
                                                </NavLinkBtn>
                                                <DangerButton type="button" onClick={() => destroy(branch.id)}>
                                                    <i className="fas fa-trash"></i>
                                                </DangerButton>
                                            </div>
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
