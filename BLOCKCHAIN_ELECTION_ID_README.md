# Blockchain Election ID Implementation

This document explains how election IDs are handled in the blockchain integration.

## The Problem

The system faced an issue where resetting the database would cause election IDs to restart from 1, but the blockchain would still remember that specific addresses had already voted in these election IDs. This resulted in the error: "Already voted in this election" even though it was a completely new election in the freshly reset database.

## The Solution

To solve this issue, we've implemented a unique blockchain election ID system that ensures IDs sent to the blockchain will never conflict, even if the database is reset multiple times.

### How It Works

1. **Unique ID Generation**: When sending a vote to the blockchain, we generate a unique election ID that's different from the database ID:
   - We use a combination of the base election ID, voter ID, and a salt value
   - The salt includes the current month/year, making IDs unique across database resets
   - The IDs are multiplied by 10,000,000 to create a large offset from the original IDs

2. **Storage & Tracking**: The system now tracks both:
   - Original database election ID (for database operations)
   - Blockchain election ID (for blockchain transactions)

3. **Verification**: When verifying votes, the system uses the blockchain election ID instead of the database ID to ensure proper verification.

### Technical Implementation

- Added a new column `blockchain_election_id` to the `blockchain_transactions` table
- Enhanced the Blockchain library's `generateUserElectionId()` method to create truly unique IDs
- Updated all blockchain transactions to use and store these unique IDs
- Modified verification methods to use blockchain election IDs for verification

### Example

If an election has ID `1` in the database, when a user with ID `42` votes, the system will generate a blockchain election ID like `10042867` (the exact algorithm creates much larger IDs). This ensures that even if the database is reset and a new election with ID `1` is created, the blockchain will treat it as an entirely different election.

## Configuration

You can adjust the salt used for generating unique IDs by setting the `blockchain.election_id_salt` variable in your `.env` file:

```
blockchain.election_id_salt = "your_custom_salt_value"
```

If not set, the system will use a default salt that includes the current month and year.
