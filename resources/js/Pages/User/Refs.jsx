import { usePage } from "@inertiajs/react";
import Container from "../../components/dashboard/Container";
import SectionSection from "../../components/dashboard/section/Section";
import SectionHeader from "../../components/dashboard/section/Header";
import SectionInner from "../../components/dashboard/section/Inner";
import Table from "../../components/dashboard/table/Table";
import UserDash from "../../components/user/dash/UserDash";

export default function Refs() {
    const { refUsers = [], refOwnerName = "User Not Found", totalRefUsers = 0 } = usePage().props;

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title="Referred User"
                        content={
                            <p>
                                You accept referrer by <strong>{refOwnerName}</strong>. And You have total{" "}
                                {totalRefUsers} referrer user.
                            </p>
                        }
                    />

                    <SectionInner>
                        <Table data={refUsers}>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Comission</th>
                                    <th>Join</th>
                                </tr>
                            </thead>
                            <tbody>
                                {refUsers.map((user) => (
                                    <tr key={user.id}>
                                        <td>{user.id}</td>
                                        <td>{user.name}</td>
                                        <td>{user.comission}</td>
                                        <td>{user.join}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    </SectionInner>
                </SectionSection>
            </Container>
        </UserDash>
    );
}

